<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seller;
use App\Vendor;
use App\User;
use App\VideoList;
use App\ImageList;
use App\Cart;
use App\BuyerOttOrders;
use DB;
use Validator;
use Symfony\Component\HttpFoundation\Response;
class OrderController extends Controller
{
    public function initateOrder(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id'=>'required',
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
        $success_check=0;
        $cart_data=Cart::where('user_id',$request->user_id)->get();
        $uniq_id=uniqid();
        $data=array();
        for($i=0;$i<count($cart_data);$i++)
        {
          if($cart_data[$i]->item_type=='image')
          {
            $data=ImageList::where(['id'=>$cart_data[$i]->item_id])->first();
          }
          else {
            $data=VideoList::where(['id'=>$cart_data[$i]->item_id])->first();
          }
          $orders=new BuyerOttOrders();
          $orders->order_id=$uniq_id;
          $orders->user_id=$request->user_id;
          $orders->seller_id=$data->seller_id;
          $orders->item_id=$cart_data[$i]->item_id;
          $orders->item_type=$cart_data[$i]->item_type;
          $orders->price=$data->price;
          $orders->save();
          $success_check=1;
        }
        if($success_check)
        {
          return response(array(
            'success'=>1,
            'order_id'=>$uniq_id,
            'msg'=>'Your request has been initiated'
          ),Response::HTTP_OK);
        }
        else {
          return response(array(
            'success'=>0,
            'order_id'=>'',
            'msg'=>'Something Went Wrong'
          ),Response::HTTP_OK);
        }
      }
    }
    public function payment(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id'=>'required',
         'tag'=>'required',
         'order_id'=>'required',
         'payment_id'=>'required'
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
        $payment_succ=BuyerOttOrders::where('order_id',$request->order_id)->update([
          'payment_id_1'=>$request->payment_id,
          'status'=>1
        ]);
        if($payment_succ)
        {
          $delete_status=Cart::where('user_id',$request->user_id)->delete();
          if($delete_status)
          {
            return response(array(
              'success'=>1,
              'msg'=>'Payment Successfull'
            ),Response::HTTP_OK);
          }
          else {
            return response(array(
              'success'=>0,
              'msg'=>'Something Went Wrong'
            ),Response::HTTP_OK);
          }
        }
        else {
          return response(array(
            'success'=>0,
            'msg'=>'Something Went Wrong'
          ),Response::HTTP_OK);
        }
      }
    }
}
