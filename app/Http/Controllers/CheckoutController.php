<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\Province;
use App\Models\Ward;
use Exception;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function placeorder( Request $request)
    {
        $order = new Order();
        $order->user_id = $request->user_id;
        $order->name = $request->input('name');
        $order->phone = $request->input('phone');
        $order->tinh = $request->input('tinh');
        $order->huyen = $request->input('huyen');
        $order->xa = $request->input('xa');
        $order->address = $request->input('address');
        if($request->input('status')){
            $file =$request->input('status');
            $order ->status = $file;
        }
        $total=0;
        $cartItems_total = Cart::where('user_id', $order->user_id)->get();
        foreach($cartItems_total as $prod){
            $total += $prod->product->price * $prod->prod_qty;
        }
        $order->total_price = $total;
        $order->save();
      

        // $order->id;
        $cartItems = Cart::where('user_id', $order->user_id)->get();
        foreach($cartItems as $item){
           OrderItems::create([
                'order_id' => $order->id,
                'prod_id'=>$item->prod_id,
                'qty' =>$item->prod_qty,
                'price'=>$item->product->price,
            ]);
            $prod = Product::where('id',$item->prod_id)->first();
            $prod->qty = $prod->qty -  $item->prod_qty;
            $prod ->update();
        }
     
        $cartItems = Cart::where('user_id', $order->user_id)->get();
        Cart::destroy($cartItems);
            
       return response()->json([
        'message'=>'Payment Success'
       ]);
    }
    public function execPostRequest($url, $data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    
    public function momo_payment(Request $request,$id){
        $total=0;
        $cartItems_total = Cart::where('user_id', $id)->get();
        foreach($cartItems_total as $prod){
            $total += $prod->product->price * $prod->prod_qty;
        }
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

    $partnerCode = 'MOMOBKUN20180529';
    $accessKey = 'klm05TvNBzhg7h7j';
    $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
    $orderInfo = "Thanh toán qua MoMo";
    $amount = $total;
    $orderId = time() ."";
    $redirectUrl = "http://localhost:3000/";
    $ipnUrl = "http://localhost:3000/";
    $extraData = "";
      

        $requestId = time() . "";
        $requestType = "payWithATM";
        // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array('partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);
        $result =$this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        // $cartItems = Cart::where('user_id', $id)->get();
        // Cart::destroy($cartItems);
        //Just a example, please check more in there
            return response()->json($jsonResult['payUrl']);
    
    }
    public function province()
    {
        $province = Province::get();
        return $province;
    }
    public function district($id){
        $district = District::where('_province_id',$id)->with('province')->get();
        return $district;
    }
    public function ward($id){
        $ward = Ward::where('_district_id',$id)->with('district')->get();
        return $ward;
    }
   

}