<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        }
        $category = new Category();
        $category->fill($request->all());
        $category->save();
        return response()->json($category,200);
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
          
            'name'=>' required|string | max:191',
            'slug'=>' required |string | max:191',
            'image'=>'required',
            
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }else{
            $category =  Category::find($id);
            $category->fill($request->all());
            $category->update();
            return response()->json([
                'success'=>true,
                'message'=>"Category Update Successfufly",
            ]);
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
            $result =Category::findOrFail($id)->delete();
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
