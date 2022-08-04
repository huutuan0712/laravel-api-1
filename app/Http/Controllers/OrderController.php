<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    
    public function index( )
    {
        $orders = Order::with('orderItems.products')->get();
        return response()->json([
            'success'=>true,
            'order'=> $orders
        ]);
    }
    public function changeStatusAdmin (Request $request){
        $id = $request->id;
        $orders = Order::find($id);
        $orders->status = $request->input('status');
        $orders->update();
        return response()->json([
            'message'=>'Change Status success'
        ]);
    }
    public function myOrder(Request $request )
    {   $id = $request->id;
        $orders = Order::with('orderItems.products')->where('user_id',$id)->get();
  
        return response()->json([
            'myOrder'=>$orders
        ]);
    }
}


