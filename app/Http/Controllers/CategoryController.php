<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Optional;

class CategoryController extends Controller
{
    public function index()
    {
            try {
                $categorys = Category::orderBy('id','desc')->get();
                if($categorys){
                    return response()->json([
                        'success'=>true,
                        'category'=>$categorys
                    ]);
                }
            } catch (Exception $e) {
                return response()->json([
                    'success'=>false,
                    'message'=>$e->getMessage(),
                ]);
        }
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
          
            'name'=>' required|string | max:191',
            'slug'=>' required |string | max:191',
            'image'=>['required'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }else{
            $category = new Category();
            if($request->hasFile('image')){
                $file =$request->file('image');
                $ext =$file->getClientOriginalExtension();
                $filename = rand().'.'.$ext;
                $file->move('assets/uploads/category/',$filename );
                $category->image ='http://127.0.0.1:8000/assets/uploads/category/' .$filename;
            }
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->save();
            if($category){
                return response()->json([
                    'success'=>true,
                    'message'=>"Category Add Successfufly",
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'message'=>"Some Problem",
                ]);
            }
        }
      
    }
    public function edit($id){
        try {
            $categorys =Category::findOrFail($id);
            if($categorys){
                return response()->json([
                    'success'=>true,
                    'categorys'=>$categorys,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage(),
            ]);
        }
    }

    public function update(Request $request,$id)
    {
       try {
        $validator = Validator::make($request->all(),[
          
            'name'=>' nullable|string | max:191',
            'slug'=>' nullable |string | max:191',
            'image'=>'nullable',
            
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }else{
            $category =Category::find($id);
            if($request->hasFile('image')){
                $path='assets/uploads/category/'.$category->image;
                if(File::exists($path)){
                    File::delete($path);
                    $file =$request->file('image');
                    $ext =$file->getClientOriginalExtension();
                    $filename= rand().'.'.$ext;
                    $file->move('assets/uploads/category/',$filename );
                    $category->image ='http://127.0.0.1:8000/assets/uploads/category/' .$filename;
                }
             
            }
           if($request->input('name')){
            $file =$request->input('name');
            $category ->name = $file;
           }
           if($request->input('slug')){
            $file =$request->input('slug');
            $category ->slug = $file;
           }
            $result= $category->update();
           if($result){
                return response()->json([
                    'success'=>true,
                    'message'=>"Category Update Successfufly",
                ]);
           }else {
            return response()->json([
                'success' => false,
                'message' => "Some Problem",
            ]);
            }
        }
      
       } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage(),
            ]);
        }   
    }
    public function destroy($id)
    {
        try {
            $result =Category::find($id)->delete();
            if($result){
                return response()->json([
                    'success'=>true,
                    'message'=>"Category Delete Successfufly",
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'message'=>"Some Problem",
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage(),
            ]);
        }
    }
    public function search($search){
        try {
            $categorys = Category::where('name','LIKE','%'.$search.'%')->orderBy('id','desc')->get();
            if($categorys){
                return response()->json([
                    'success'=>true,
                    'categorys'=>$categorys,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage(),
            ]);
        }
    }
}
