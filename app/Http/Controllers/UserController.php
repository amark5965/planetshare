<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use Storage;
use Symfony\Component\HttpFoundation\Response;
class UserController extends Controller
{
    public function getUserList(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id' => 'required',
         'tag'=>'required',
     ]);
     if ($validator->fails())
     {
         return response(array(
           'success'=>0,
           'data'=>$validator->errors()
         ));
     }
     else {
       $limit=10;
       if(!is_null($request->limit))
       {
         $limit=$request->limit;
       }
        $userlist=User::where('id','!=',1)->paginate($limit);
        if($userlist)
        {
          return response(array(
            'success'=>1,
            'data'=>$userlist,
            'msg'=>'user List'
          ),Response::HTTP_OK);
        }
        else
        {
          return response(array(
            'success'=>0,
            'msg'=>'Something Went Wrong'
          ),Response::HTTP_UNAUTHORIZED);
        }
    }
  }
}
