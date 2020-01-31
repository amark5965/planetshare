<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Auth;
use App\Seller;
use App\Vendor;
use Symfony\Component\HttpFoundation\Response;
use Concerns\InteractsWithInput;
class AuthenticateController extends Controller
{
  // public function __construct()
  // {
  //   $this->middleware('auth:api')->except('login','register');
  // }
  public function register(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'name' => 'required',
       'email'=>'unique:users|required',
       'password'=>'required',
       'tag'=>'required'
   ]);
   if ($validator->fails())
   {
       return response(array(
         'success'=>0,
         'data'=>$validator->errors()
       ));
   }
   else {
     $name=$request->name;
     $email=$request->email;
     $password=bcrypt($request->password);
     $user=new User();
     $user->Accountid=uniqid();
     $user->name=$name;
     $user->email=$email;
     $user->password=$password;
     $user->save();
     if($user)
     {
       return response(array(
         'success'=>1,
         'msg'=>'Successfully Created'
       ),Response::HTTP_OK);
     }
     else {
       return response(array(
         'success'=>0,
         'msg'=>'Something Went Wrong'
       ),Response::HTTP_OK);
     }
   }
  }
  public function login(Request $request)
  {
      $validator=Validator::make($request->all(), [
         'email'=>'required',
         'password'=>'required',
         'tag'=>'required'
     ]);
     if ($validator->fails())
     {
         return response(array(
           'success'=>0,
           'data'=>$validator->errors()
         ));
     }
     else {
       $tag=$request->tag;
       $email=$request->email;
       $password=$request->password;
       // if($tag=="dash")
       // {
       //   $check=Auth::attempt(['email'=>$email,'password'=>$password,'user_role'=>'a']);
       // }
       // else {
       //   $check=Auth::attempt(['email'=>$email,'password'=>$password,'user_role'=>'u']);
       // }
        $check=Auth::attempt(['email'=>$email,'password'=>$password,'status'=>1]);
       if($check)
       {
         $user =  Auth::user();
         $token = $user->createToken($tag)->accessToken;
         $seller_id=0;
         $vendor_id=0;
         $seller_id_desc=Seller::where('user_id',Auth::user()->id)->first();
         if($seller_id_desc)
         {
           $seller_id=$seller_id_desc->id;
         }
         $vendor_id_desc=Vendor::where('user_id',Auth::user()->id)->first();
         if($vendor_id_desc)
         {
           $vendor_id=$vendor_id_desc->id;
         }
         return response(array(
           'success'=>1,
           'seller_id'=>$seller_id,
           'vendor_id'=>$vendor_id,
           'user_id'=>Auth::user()->id,
           'email'=>Auth::user()->email,
           'name'=>Auth::user()->name,
           'token'=>$token
         ),Response::HTTP_OK);
       }
       else {
         return response(array(
            'success'=>0,
            'msg'=>'Something Went Wrong'
         ),Response::HTTP_OK);
       }
     }
  }
  public function logout(Request $request)
  {
    //$token = $request->bearerToken();
    // $token = $request->user()->token();
    // $token->revoke();
    $logout=$request->user()->token()->revoke();
    if($logout)
    {
      return response(array(
        'success'=>1,
        'msg'=>'You have been succesfully logged out!'
      ),Response::HTTP_OK);
    }
    else {
      return response(array(
        'success'=>0,
        'msg'=>'Something Went Wromg'
      ),Response::HTTP_OK);
    }
  }
}
