<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
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
            $product = new Product();
            $product->fill($request->all());
            $product->save();
            return response()->json([
                'success'=>true,
                'message'=>"Product Add Successfufly",
            ]);
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
            $product->fill($request->all());
            $product->update();
            return response()->json([
                'success'=>true,
                'message'=>"Product Update Successfufly",
            ]);
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
