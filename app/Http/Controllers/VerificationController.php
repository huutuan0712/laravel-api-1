<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id)
    {
        // if(!$request ->hasValidSignature()){
        //     return response()->json(['msg'=>"Invalid / Expired url provided"],401);
        // }
        $user = User::find($user_id);

        if(!$user ->hasVerifiedEmail()){
            $user ->markEmailAsVerified();
        }else{
            return response()->json([
                'status'=>401,
                'message'=> "Email already verified"
            ],400);
        }
        return response()->json([
            'status'=>200,
            'message'=> "You email $user->email successfully verified"
        ],400);
    }
}
