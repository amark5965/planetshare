<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
class LocationController extends Controller
{
    public function getCountry()
    {
      $data=DB::table('countries')->get();
      if($data)
      {
        return response(array(
          'success'=>1,
          'data'=>$data
        ));
      }
      else {
        return response(array(
          'success'=>0,
          'data'=>[]
        ));
      }
    }
    public function getState(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'country_id' => 'required',
     ]);
     if ($validator->fails())
     {
         return response(array(
           'success'=>0,
           'data'=>$validator->errors()
         ));
     }
     else {
      $data=DB::table('states')->where('country_id',$request->country_id)->get();
      if($data)
      {
        return response(array(
          'success'=>1,
          'data'=>$data
        ));
      }
      else {
        return response(array(
          'success'=>0,
          'data'=>[]
        ));
      }
    }
    }
    public function getCity(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'state_id' => 'required',
     ]);
     if ($validator->fails())
     {
         return response(array(
           'success'=>0,
           'data'=>$validator->errors()
         ));
     }
     else {
      $data=DB::table('cities')->where('state_id',$request->state_id)->get();
      if($data)
      {
        return response(array(
          'success'=>1,
          'data'=>$data
        ));
      }
      else {
        return response(array(
          'success'=>0,
          'data'=>[]
        ));
      }
    }
  }
}
