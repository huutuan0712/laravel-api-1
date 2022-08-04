<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\VerificationController;
use App\Http\Requests\RequestHelper;
use App\Mail\MailController;
use App\Mail\SendMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => [
            'login','register','logout','updateInformation','changePassWord','forgotPassword','updatePassword']]);
    
    }
    
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json([
            'error' => 'Unauthorized',
            'message'=> 'Email or password not dafaut'
        ], 401);
        }
        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

            try {
                if($user){
                    Mail::mailer('smtp')->to($user->email)->send(new MailController($user));

                    return response()->json([
                        'message' => 'User successfully registered',
                        'user' => $user
                    ], 201);
                }
            } catch (\Exception $th) {
                return response()->json([
                    'message' => 'could not send email verification email,plase try again', 
                ], 201);
            }
        
        
    }
   
    
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    public function updateInformation(Request $request) {
        $id =$request->id;
        $user = User::find($id);
        if($user){
                if($request->hasFile('image')){
                    $file =$request->file('image');
                    $ext =$file->getClientOriginalExtension();
                    $filename= rand().'.'.$ext;
                    $file->move('assets/uploads/user/',$filename );
                    $user->avatar ='http://127.0.0.1:8000/assets/uploads/user/' .$filename;
                }
                if($request->input('name')){
                    $file =$request->input('name');
                    $user ->name = $file;
                }
                if($request->input('address')){
                    $file =$request->input('address');
                    $user ->address = $file;
                }
                if($request->input('birthday')){
                    $file =$request->input('birthday');
                    $user ->birthday = $file;
                }
                if($request->input('phone')){
                    $file =$request->input('phone');
                    $user ->phone = $file;
                }
            
            $user->update();
        }else{
            return response()->json([
                'success' => false,
                'message' => "Some Problem",
            ]);
        }
        return response()->json(['user'=>$user]);
    }


    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function changePassWord(Request $request) {
        // $validator = Validator::make($request->all(), [
        //     'newPassword' => 'required|string|confirmed|min:6',
        //     'confirmPassword' => 'required|min:6',
        //     'oldPassword' => 'required|min:6'
        // ]);
        $userId = $request->id;
        $newPassword =$request->newPassword;
        $user = User::where('id', $userId)->update(
            ['password' => bcrypt( $newPassword)]
        );
        return response()->json([
            'message' => 'User successfully changed password',
        ], 201);
    }
    // FORGOT PASSWORD
     public function forgotPassword(Request $request){
        if(!$this->validEmail($request->email)) {
            return response()->json([
                'message' => 'Email not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $this->sendEmail($request->email);
            return response()->json([
                'message' => 'Password reset mail has been sent.'
            ], Response::HTTP_OK);            
        }
    }
    public function sendEmail ($email){
        $token =$this ->createToken($email);
        Mail::to($email)->send(new SendMail($token));
    }
    public function validEmail($email) {
        return !!User::where('email', $email)->first();
     }

    public function createToken($email){
        $isToken = DB::table('password_resets')->where('email', $email)->first();
  
        if($isToken) {
          return $isToken->token;
        }
  
        $token = Str::random(80);;
        $this->saveToken($token, $email);
        return $token;
      }
      public function saveToken($token, $email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()            
        ]);
    }
    //UPDATE PASSWORD
    public function updatePassword(RequestHelper $request){
        return $this->validateToken($request)->count() > 0 ? $this->DOIPASSWORD($request) : $this->noToken();
    }

    private function validateToken($request){
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token
        ]);
    }

    private function noToken() {
        return response()->json([
          'error' => 'Email or token does not exist.'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function DOIPASSWORD ($request) {
        $user = User::whereEmail($request->email)->first();
        $user->update([
          'password'=>bcrypt($request->password)
        ]);
        $this->validateToken($request)->delete();
        return response()->json([
          'message' => 'Password changed successfully.'
        ],Response::HTTP_CREATED);
    }  
}
