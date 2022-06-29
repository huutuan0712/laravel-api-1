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
        $product = Product::orderBy('id','desc')->with('category')->get();
        return response()->json($product,200);
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
            'cate_id'=>' required|unique:categories,id',
            'name'=>' required|string | max:191',
            'slug'=>' required |string | max:191',
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
            $filename ="";
            if($request->file('image')){
                $filename =$request->file('image')->store('product', 'public');
            }else{
                $filename=null;
            }
            $product =  Product::create([
               'cate_id'=>$request->cate_id,
               'name'=>$request->name,
               'slug'=>$request->name,
               'description'=>$request->name,
               'price'=>$request->name,
               'image'=>$filename,
               'qty'=>$request->name,

            ]);
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
            'name'=>' required|string | max:191',
            'slug'=>' required |string | max:191',
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
            $product = Product::find($id);
            $filename = "";
            $destination = public_path('storage\\' . $product->image);
            if ($request->file('new_image')) {
                if (File::exists($destination)) {
                    File::delete($destination);
                }
                $filename = $request->file('new_image')->store('product', 'public');
            } else {
                $filename = $request->old_image;
            }
            $product->cate_id = $request->cate_id;
            $product->name = $request->name;
            $product->slug = $request->slug;
            $product->price = $request->price;
            $product->image = $filename;
            $product->qty = $request->qty;
            $result = $product->save();
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
