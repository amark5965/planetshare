<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seller;
use App\Vendor;
use App\User;
use App\VideoList;
use App\ImageList;
use App\Cart;
use DB;
use Validator;
use Storage;
use App\BuyerCollection;
use Symfony\Component\HttpFoundation\Response;
class CartWishlistController extends Controller
{
    public function createCollection(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'collection_type'=>'required',   //---------image or video
         'user_id'=>'required',
         'collection_name'=>'required',
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
        $createcollection=new BuyerCollection();
        $createcollection->Request_id=uniqid();
        $createcollection->collection_name=$request->collection_name;
        $createcollection->collection_type=$request->collection_type;
        $createcollection->user_id=$request->user_id;
        $createcollection->save();
        if($createcollection)
        {
          return response(array(
            'success'=>1,
            'msg'=>'Collection Created'
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
    public function insertIntoCollection(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'item_id' => 'required',
         'item_type'=>'required',   //---------image or video
         'user_id'=>'required',
         'collection_id'=>'required',
         'cart_type'=>'required',  //-------------cart or wishlist
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

      $user_id=$request->user_id;
      $status_check=User::where('id',$user_id)->count();
      if($status_check==1)
      {
          $cart_status=Cart::where(['user_id'=>$user_id,'item_id'=>$request->item_id,'item_type'=>$request->item_type,'cart_type'=>$request->cart_type,'buyer_collections_id'=>$request->collection_id])->count();
          if($cart_status!=0)
          {
            return response(array(
              'success'=>2,
              'data'=>[],
              'msg'=>'Already Added To Collection'
            ),Response::HTTP_OK);
          }
          else {
          $cart=new Cart();
          $cart->request_id=uniqid();
          $cart->user_id=$user_id;
          $cart->buyer_collections_id=$request->collection_id;
          $cart->item_id=$request->item_id;
          $cart->item_type=$request->item_type;
          $cart->cart_type=$request->cart_type;
          $cart->save();
          if($cart)
          {
            return response(array(
              'success'=>1,
              'data'=>[],
              'msg'=>'Item Added Successfully'
            ),Response::HTTP_OK);
          }
          else
           {
             return response(array(
               'success'=>0,
               'data'=>[],
               'msg'=>'Something Went Wrong'
             ),Response::HTTP_OK);
          }
      }
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
/*    public function getCartInfo(Request $request)
    {
      $validator=Validator::make($request->all(),
      [
         'user_id'=>'required',
         'cart_type'=>'required',  //-------------cart or wishlist
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
        $data_image=Cart::where(['user_id'=>$request->user_id,'cart_type'=>$request->cart_type,'item_type'=>'image'])->with('imageItemInfo')->get();
        $data_video=Cart::where(['user_id'=>$request->user_id,'cart_type'=>$request->cart_type,'item_type'=>'video'])->with('videoItemInfo')->get();
        $data=array();
        $data['image_gallery']=$data_image;
        $data['video_gallery']=$data_video;
        $price=0;
        $price1;
        for($i=0;$i<count($data_image);$i++)
        {
          $price1=json_decode($data_image[$i]);
          $price=$price+$price1->image_item_info->price;
        }
        for($j=0;$j<count($data_video);$j++)
        {
          $price1=json_decode($data_video[$j]);
            $price=$price+$price1->video_item_info->price;
        }
        if($data)
        {
          return response(array(
            'success'=>1,
            'data'=>$data,
            'price'=>$price,
            'msg'=>'Cart data'
          ),Response::HTTP_OK);
        }
        else
         {
           return response(array(
             'success'=>0,
             'data'=>[],
             'msg'=>'Something Went Wrong'
           ),Response::HTTP_OK);
        }
      }
    }*/
    public function getUserCollection(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id'=>'required',
         'collection_type'=>'required',
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
        $collection_type=$request->collection_type;
        $buyercollection=BuyerCollection::select('id','collection_name','created_at')->where(['user_id'=>$request->user_id,'collection_type'=>$collection_type])->get();
        if($buyercollection)
        {
          return response(array(
            'success'=>1,
            'data'=>$buyercollection,
            'msg'=>'Collection List'
          ),Response::HTTP_OK);
        }
        else
         {
           return response(array(
             'success'=>0,
             'data'=>[],
             'msg'=>'Something Went Wrong'
           ),Response::HTTP_OK);
         }
        }
      }


      public function getUserCollectionData(Request $request)
      {
        $validator=Validator::make($request->all(), [
           'user_id'=>'required',
           'collection_type'=>'required',
           'collection_id'=>'required',
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
          $collection_type=$request->collection_type;
          $data=array();
          if($collection_type=="video")
          {
            $data=Cart::select('request_id','id','item_id','item_type','created_at')->where(['user_id'=>$request->user_id,'buyer_collections_id'=>$request->collection_id,'item_type'=>'video'])->with('videoItemInfo')->get();
          }
          else if($collection_type=="image")
          {
            $data=Cart::select('request_id','id','item_id','item_type','created_at')->where(['user_id'=>$request->user_id,'buyer_collections_id'=>$request->collection_id,'item_type'=>'image'])->with('imageItemInfo')->get();
          }
          else
           {
             return response(array(
               'success'=>0,
               'data'=>[],
               'msg'=>'Something Went Wrong'
             ),Response::HTTP_OK);
           }
          if($data)
          {
            return response(array(
              'success'=>1,
              'data'=>$data,
              'msg'=>'Collection Data'
            ),Response::HTTP_OK);
          }
          else
           {
             return response(array(
               'success'=>0,
               'data'=>[],
               'msg'=>'Something Went Wrong'
             ),Response::HTTP_OK);
           }
          }
        }
}
