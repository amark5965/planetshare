<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Auth;
use DB;
use App\Seller;
use App\ImageList;
use App\VideoList;
use App\BuyerOttOrders;
use Symfony\Component\HttpFoundation\Response;
class SellerController extends Controller
{
    public function createSeller(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id' => 'required',
         'display_name'=>'required',
         'phone_number'=>'required',
         'company_info'=>'required',
         'terms_condition'=>'required',
         'country'=>'required',
         'address'=>'required',
         'city'=>'required',
         'pincode'=>'required',
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
        $created_by=$request->tag;
        $display_name=$request->display_name;
        $phone_number=$request->phone_number;
        $terms_condition=$request->terms_condition;
        $company_info=$request->company_info;
        $country=$request->country;
        $address=$request->address;
        $city=$request->city;
        $pincode=$request->pincode;
        $seller=new Seller();
        $seller->Accountid=uniqid();
        $seller->user_id=$user_id;
        $seller->display_name=$display_name;
        $seller->phone_number=$phone_number;
        $seller->company_info=$company_info;
        $seller->address=$address;
        $seller->city=$city;
        $seller->country=$country;
        $seller->pincode=$pincode;
        $seller->created_by=$created_by;
        $seller->term_condition=$terms_condition;
        $seller->save();
        if($seller)
        {
          return response(array(
            'success'=>1,
            'seller_id'=>$seller->id,
            'msg'=>'Successfully Created'
          ),Response::HTTP_OK);
        }
        else {
          return response(array(
            'success'=>0,
            'data'=>[],
            'msg'=>'Something Went Wrong'
          ),Response::HTTP_UNAUTHORIZED);
        }
     }
    }
    public function addSellerBankDetails(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id' => 'required',
         'seller_id'=>'required',
         'bank_name'=>'required',
         'account_holder_name'=>'required',
         'account_number'=>'required',
         'ifsc_code'=>'required',
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
       $seller=Seller::where('id',$request->seller_id)->update([
         'account_holder_name'=>$request->account_holder_name,
         'ifsc_code'=>$request->ifsc_code,
         'account_number'=>$request->account_number,
         'bank_name'=>$request->bank_name,
       ]);
       if($seller)
       {
         return response(array(
           'success'=>1,
           'msg'=>'Bank Details Updated Successfully'
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
    public function getSellerList(Request $request)
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
        $sellerlist=Seller::paginate($limit);
        if($sellerlist)
        {
          return response(array(
            'success'=>1,
            'data'=>$sellerlist,
            'msg'=>'Seller List'
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
  public function getSellerVideoList(Request $request)
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
      $sellervideolist=VideoList::orderBy('created_at','desc')->paginate($limit);
      if($sellervideolist)
      {
        return response(array(
          'success'=>1,
          'data'=>$sellervideolist,
          'msg'=>'Seller List'
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
public function getSellerImageList(Request $request)
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
    $selleimagerlist=ImageList::orderBy('created_at','desc')->paginate($limit);
    if($selleimagerlist)
    {
      return response(array(
        'success'=>1,
        'data'=>$selleimagerlist,
        'msg'=>'Seller List'
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
  //-------------GET VENDOR GIGS TABS--------------//
    public function getSellerImageTabs(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id' => 'required',
         'seller_id' => 'required',
         'tab_index'=>'required',
         'tag'=>'required',
         'content_type'=>'required'
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
       if($request->content_type=="image")
       {
         $inqueue=ImageList::where(['status'=>4,'seller_id'=>$request->seller_id])->count();
         $active=ImageList::where(['status'=>1,'seller_id'=>$request->seller_id])->count();
         $inactive=ImageList::where(['status'=>0,'seller_id'=>$request->seller_id])->count();
         $pending=ImageList::where(['status'=>2,'seller_id'=>$request->seller_id])->count();
         $disapproved=ImageList::where(['status'=>3,'seller_id'=>$request->seller_id])->count();
         $tabs=array();
         $tabs[0]['name']='INQUEUE';
         $tabs[0]['count']=$inqueue;
         $tabs[0]['id']=4;
         $tabs[1]['name']='ACTIVE';
         $tabs[1]['count']=$active;
         $tabs[1]['id']=1;
         $tabs[2]['name']='PENDING APPROVAL';
         $tabs[2]['count']=$pending;
         $tabs[2]['id']=2;
         $tabs[3]['name']='DENIED';
         $tabs[3]['count']=$disapproved;
         $tabs[3]['id']=3;
         $tabs[4]['name']='PAUSED';
         $tabs[4]['count']=$inactive;
         $tabs[4]['id']=0;
         $limit=10;
         if(!is_null($limit))
         {
           $limit=$request->limit;
         }
         $tab_data=ImageList::where(['status'=>$request->tab_index,'seller_id'=>$request->seller_id])->orderBy('created_at','desc')->paginate($limit);
         if($tab_data)
         {
           return response(array(
             'success'=>1,
             'tabs'=>$tabs,
             'tab_data'=>$tab_data,
           ),Response::HTTP_OK);
         }
         else {
           return response(array(
             'success'=>0,
             'tabs'=>[],
             'tab_data'=>[],
             'msg'=>'Something Went Wrong'
           ),Response::HTTP_OK);
         }
       }
         else
         {
               $inqueue=VideoList::where(['status'=>4,'seller_id'=>$request->seller_id])->count();
               $active=VideoList::where(['status'=>1,'seller_id'=>$request->seller_id])->count();
               $inactive=VideoList::where(['status'=>0,'seller_id'=>$request->seller_id])->count();
               $pending=VideoList::where(['status'=>2,'seller_id'=>$request->seller_id])->count();
               $disapproved=VideoList::where(['status'=>3,'seller_id'=>$request->seller_id])->count();
               $tabs=array();
               $tabs[0]['name']='INQUEUE';
               $tabs[0]['count']=$inqueue;
               $tabs[0]['id']=4;
               $tabs[1]['name']='ACTIVE';
               $tabs[1]['count']=$active;
               $tabs[1]['id']=1;
               $tabs[2]['name']='PENDING APPROVAL';
               $tabs[2]['count']=$pending;
               $tabs[2]['id']=2;
               $tabs[3]['name']='DENIED';
               $tabs[3]['count']=$disapproved;
               $tabs[3]['id']=3;
               $tabs[4]['name']='PAUSED';
               $tabs[4]['count']=$inactive;
               $tabs[4]['id']=0;
               $limit=10;
               if(!is_null($limit))
               {
                 $limit=$request->limit;
               }
               $tab_data=VideoList::where(['status'=>$request->tab_index,'seller_id'=>$request->seller_id])->orderBy('created_at','desc')->paginate($limit);
               if($tab_data)
               {
                 return response(array(
                   'success'=>1,
                   'tabs'=>$tabs,
                   'tab_data'=>$tab_data,
                 ),Response::HTTP_OK);
               }
               else {
                 return response(array(
                   'success'=>0,
                   'tabs'=>[],
                   'tab_data'=>[],
                   'msg'=>'Something Went Wrong'
                 ),Response::HTTP_OK);
               }
         }
       }
  }
  //----------------------END----------------------//
  //------------------------CHANGE SELLER GIG STATUS-----------------
      public function sellerChangeStatus(Request $request)
      {
        $validator=Validator::make($request->all(), [
           'user_id' => 'required',
           'seller_id' => 'required',
           'tag'=>'required',
           'cat_id'=>'required',
           'status'=>'required',
           'content_type'=>'required'
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
            if($request->content_type=='image')
            {
              $response=ImageList::where(['id'=>$request->cat_id,'seller_id'=>$request->seller_id])->update([
                'status'=>$request->status
              ]);
              if($response)
              {
                return response(array(
                  'success'=>1,
                  'msg'=>'Successfully Updated'
                ));
              }
              else {
                return response(array(
                  'success'=>0,
                  'msg'=>'Something Went Wrong'
                ));
              }
            }
            else {
              $response=VideoList::where(['id'=>$request->cat_id,'seller_id'=>$request->seller_id])->update([
                'status'=>$request->status
              ]);
              if($response)
              {
                return response(array(
                  'success'=>1,
                  'msg'=>'Successfully Updated'
                ));
              }
              else {
                return response(array(
                  'success'=>0,
                  'msg'=>'Something Went Wrong'
                ));
              }
            }

       }
      }
      //-----------------------END_------------------------
      //-----------SELLER ORDERS---------------------//
      public function sellerOrders(Request $request)
      {
        $validator=Validator::make($request->all(), [
           'user_id' => 'required',
           'seller_id' => 'required',
           'item_type'=>'required',
           'tag'=>'required',
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
         $item_type=$request->item_type;
         $limit=10;
         if(!is_null($request->limit))
         {
           $limit=$request->limit;
         }
         if($item_type=="image")
         {
           //DB::enableQueryLog();
           $data= BuyerOttOrders::select('id','item_id','order_id','item_type','price','created_at')->where(['seller_id'=>$request->seller_id,'status'=>'1','item_type'=>'image'])->where('order_id','like',"%".$request->keywords."%")->with('imageItemInfo')->paginate($limit);
           //$data=DB::table('buyer_ott_orders')->select('buyer_ott_orders.id','buyer_ott_orders.item_id','image_lists.large_thumb')->join('image_lists','image_lists.id','=','buyer_ott_orders.item_id')->where(['buyer_ott_orders.status'=>1,'buyer_ott_orders.seller_id'=>$request->seller_id])->get();
          // dd(DB::getQueryLog());
         }
         else if($item_type=="video")
         {
           $data=BuyerOttOrders::select('id','item_id','order_id','item_type','price','created_at')->where(['seller_id'=>$request->seller_id,'status'=>'1','item_type'=>'video'])->where('order_id','like',"%".$request->keywords."%")->with('videoItemInfo')->paginate($limit);
         }
         else
         {
           $order_image=BuyerOttOrders::where(['seller_id'=>$request->seller_id,'status'=>'1','item_type'=>'image'])->with('imageItemInfo')->get();
           $order_video=BuyerOttOrders::where(['seller_id'=>$request->seller_id,'status'=>'1','item_type'=>'video'])->with('videoItemInfo')->get();
           if($order_image && $order_video)
           {
             return response(array(
               'success'=>1,
               'order_image'=>$order_image->makeHidden(['payment_id_1','payment_id_2','payment_id_3','updated_at','status']),
               'order_video'=>$order_video->makeHidden(['payment_id_1','payment_id_2','payment_id_3','updated_at','status']),
             ),Response::HTTP_OK);
           }
           else {
             return response(array(
               'success'=>0,
               'msg'=>'Something Went Wrong'
             ),Response::HTTP_OK);
           }
         }
         if($data)
         {
           return response(array(
             'success'=>1,
             'data'=>$data,
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
      //---------------------END-----------------------//
}
