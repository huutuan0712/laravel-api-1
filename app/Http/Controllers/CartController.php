<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addProduct(Request $request){
        $product_id =$request->input('idProduct');
        $product_qty =$request->input('qty');
        $user_id =$request->input('user_id');
        if(isset($user_id)){
            $prod_check = Product::where('id',$product_id)->first();
            if($prod_check){
                if(Cart::where('prod_id',$product_id)->where('user_id',$user_id)->exists()){
                    $cartItems =Cart::where('user_id',$user_id)->with('product')->get();
                    return response()->json([
                        'message'=>'Add To Cart',
                    ]);
                }
                else
                {
                    $cartItem = new Cart();
                    $cartItem ->prod_id = $product_id;
                    $cartItem ->user_id =$user_id;
                    $cartItem ->prod_qty = $product_qty;
                    $cartItem->save();
                    return response()->json([
                        'cart'=>$cartItem
                    ]);
                }
               
            }
        }else{
            return response()->json(['status' => "Login to Countnue"]);
        }
    }
    public function viewCart($id){
        try {
            $cartItems =Cart::where('user_id',$id)->with('product')->get();
            if($cartItems){
                return response()->json([
                    'success'=>true,
                    'cartItem'=>$cartItems
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage(),
            ]);
    }
        // $cartItems =Cart::where('user_id',$id)->with('product')->get();
        // return response()->json(['cartItem'=>$cartItems]);
    }
    public function deleteProduct(Request $request){
        $user_id = $request->id;
        $prod_id= $request->prod_id;
        // dd($user_id,$prod_id);
        if( $user_id){  
            // dd(Cart::where('prod_id',$prod_id)->where('user_id', $user_id)->exists());
            if(Cart::where('prod_id',$prod_id)->where('user_id', $user_id)->exists()){
                $cartItem = Cart::where('prod_id',$prod_id)->where('user_id',  $user_id)->first();
                $cartItem->delete();
                return response()->json(['message'=>"Product Delete"]);
            }
        }else{
            return response()->json(['message' => "Login to Countnue"]);
        }
    
    }
    public function updateProduct(Request $request){
        $prod_id=$request->prod_id;
        $product_qty= $request->prod_qty;
        $user_id = $request->id;
      
        if($user_id){
            if(Cart::where('id',$prod_id)->where('user_id', $user_id)->exists()){
                $cart = Cart::where('id',$prod_id)->where('user_id',  $user_id)->first();
                $cart ->prod_qty = $product_qty;
                $cart->update();
                return response()->json(['message' => "Quatity Update"]);
            }
        }
        else{
            return response()->json(['message' => "Login to Countnue"]);
        }
    }
    public function cartCount(Request $request)
    {   $id= $request->id;
        $cartcount = Cart::where('user_id', $id)->count();
        return response()->json(['count'=> $cartcount]);
    }
    public function totalCart(Request $request)
    {   $id= $request->id;
        $total=0;
        $cartItems_total = Cart::where('user_id', $id)->get();
        foreach($cartItems_total as $prod){
            $total += $prod->product->price * $prod->prod_qty;
        }
        return response()->json($total);
    }

   
   
}
