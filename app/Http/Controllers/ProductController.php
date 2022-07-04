<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {
      
        try {
            $product = Product::orderBy('id','desc')->with('category')->get();
            if($product){
                return response()->json([
                    'success'=>true,
                    'product'=>$product
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage(),
            ]);
    }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       try {
        $validator = Validator::make($request->all(),[
            'cate_id'=>' required',
            'name'=>' required|string | max:191',
            'slug'=>' required |string | max:191',
            'size'=>' required |string | max:191',
            'description'=>' required |string | max:191',
            'price'=>' required |string | max:191',
            'image'=>['required'],
            'qty'=>' required |string | max:191',
           
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }else{
            $product = new Product();
            if($request->hasFile('image')){
                $file =$request->file('image');
                $ext =$file->getClientOriginalExtension();
                $filename= time().'.'.$ext;
                $file->move('assets/uploads/product/',$filename );
                $product->image = 'http://127.0.0.1:8000/assets/uploads/product/'.$filename;
            }
            // foreach($request->file('image') as $file){
            //     $ext =$file->getClientOriginalExtension();
            //     $filename= time().'.'.$ext;
            //     $file->move('assets/uploads/product/',$filename );
            //     $product->image = 'http://127.0.0.1:8000/assets/uploads/product/'.$filename;
            //     product_Image::create([
            //         'product_id'=>$product->id,
            //         'filename'=>$filename
            //     ]);
            // }
            $product->cate_id = $request->cate_id;
            $product->name = $request->name;
            $product->slug = $request->slug;
            $product->size = $request->size;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->qty = $request->qty;
            $product->save();
            if($product){
                return response()->json([
                    'success'=>true,
                    'message'=>"Product Add Successfufly",
                ]);
            }else{
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

    public function edit($id){
        try {
            $products =Product::findOrFail($id);
            if($products){
                return response()->json([
                    'success'=>true,
                    'product'=>$products,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage(),
            ]);
        }
    }
    public function update(Request $request, $id)
    {
      try {
        $validator = Validator::make($request->all(),[
            'cate_id'=>' required|unique:categories,id',
            'name'=>' nullable|string | max:191',
            'slug'=>' nullable |string | max:191',
            'description'=>' nullable |string | max:191',
            'price'=>' nullable |string | max:191',
            'image'=>'nullable',
            'qty'=>'required |string | max:191',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }else{
            $product = Product::find($id);
            if($request->hasFile('image')){
                $path='assets/uploads/product/'.$product->image;
                if(File::exists($path)){
                    File::delete($path);
                    $file =$request->file('image');
                    $ext =$file->getClientOriginalExtension();
                    $filename= time().'.'.$ext;
                    $file->move('assets/uploads/category/',$filename );
                    $product->image ='http://127.0.0.1:8000/assets/uploads/product/' .$filename;
                }else{
                    $file =$request->file('image');
                    $ext =$file->getClientOriginalExtension();
                    $filename= time().'.'.$ext;
                    $file->move('assets/uploads/category/',$filename );
                    $product->image ='http://127.0.0.1:8000/assets/uploads/product/' .$filename;
                }
            }
            if($request->input('cate_id')){
                $file =$request->input('cate_id');
                $product ->cate_id = $file;
               }
               
            if($request->input('name')){
                $file =$request->input('name');
                $product ->name = $file;
            }
            if($request->input('slug')){
                $file =$request->input('slug');
                $product ->slug = $file;
            }
            if($request->input('size')){
                $file =$request->input('size');
                $product ->size = $file;
            }
            if($request->input('description')){
                $file =$request->input('description');
                $product ->description = $file;
            }
            if($request->input('price')){
                $file =$request->input('price');
                $product ->price = $file;
            }
            if($request->input('qty')){
                $file =$request->input('qty');
                $product ->qty = $file;
            }
           
           $result= $product->update();
            if($result){
                return response()->json([
                    'success'=>true,
                    'message'=>"Product Update Successfufly",
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $result =Product::findOrFail($id)->delete();
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
            $products = Product::with('category')->where('name','LIKE','%'.$search.'%')->orderBy('id','desc')->get();
            if($products){
                return response()->json([
                    'success'=>true,
                    'products'=>$products,
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
