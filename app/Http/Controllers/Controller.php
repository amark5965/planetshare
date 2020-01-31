<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\VendorLanguage;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function test1()
    {
        $value= $request->data;
        $x= json_decode($value,true);
          foreach( $x as $y)
          {
             $language= new  VendorLanguage();
              $language->lang_code=$y->code;
              $language->lang_name=$y->name;
               $language->save();
          }
          if($language)
          {
            return response(array(
              'success'=>1,
              'msg'=>'Added Successfully'
            ),Response::HTTP_OK);
          }
          else {
            return response(array(
              'success'=>0,
              'msg'=>'Something Went Wrong'
            ),Response::HTTP_UNAUTHORIZED);
          }
    }
}
