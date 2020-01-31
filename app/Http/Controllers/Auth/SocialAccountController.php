<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\SocialAccount;
use App\User;
use Validator;
use Symfony\Component\HttpFoundation\Response;

class SocialAccountController extends Controller
{
    public function findOrCreateUser(Request $request)
    {
    	$validator=Validator::make($request->all(), [
            'provider_name' => 'required',
            //'provider_id'=>'required',
            'name'=>'required',
            'tag'=>'required'
        ]);
           
        if ($validator->fails())
        {
            return response(array(
                'success'=>0,
                'data'=>$validator->errors()
            ));
        }
        else
        {
        	$provider_name = $request->provider_name;
        	$provider_id = $request->provider_id;
        	$name = $request->name;
        	$email = $request->email;
        	$account_id = uniqid();
            //dd($account_id);
            
        	$account = SocialAccount::where('provider_name', $provider_name)->where('provider_id',$provider_id)->first();
        	if ($account)
            {
                $token = $account->createToken('User personal access token')->accessToken;
                dd($token);
                return response(array(
                    'success'=>1,
                    'seller_id'=>$seller_id,
                    'vendor_id'=>$vendor_id,
                    
                ),Response::HTTP_OK);
            }
            
            else
            {

                $user = User::where('email', $email)->first();
                if (! $user)
                {
                    $user = User::create([
                        'email'=>$email,
                        'name'=>$name,
                        'Accountid'=>$account_id,
                    ]);
                }
                $user->accounts()->create([
                    'provider_name' => $provider_name,
                    'provider_id' => $provider_id,
                ]);
                $social = SocialAccount::where('user_id',$user->id)->first();
                if($user)
                {
                    return response(array(
                        'success'=>1,
                        'seller_id'=>$seller_id,
                        'vendor_id'=>$vendor_id,
                        'email'=>$user->email,
                        'name'=>$user->name,
                        'provider_name'=>$social->provider_name,
                        'provider_id'=>$social->provider_id,
                    ),Response::HTTP_OK);
                }
                else
                {
                    return response(array(
                        'success'=>0,
                        'msg'=>'Something Went Wrong'
                    ),Response::HTTP_OK);
                }
                
            }
        }
    }
}
