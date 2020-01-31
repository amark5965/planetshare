<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image_flav;
use Validator;
use App\Seller;
use App\Vendor;
use App\User;
use Storage;
use App\VideoList;
use App\ImageList;
use App\ImageCategory;
use App\VideoCategory;
use App\VendorServiceList;
use App\VendorServiceGig;
use App\VendorLanguage;
use DB;
use App\BuyerOttOrders;
use App\Cart;
use App\Http\Resources\Category\ImageCategoryResource;
use App\Http\Resources\Category\VideoCategoryResource;
use Symfony\Component\HttpFoundation\Response;
class CommonController extends Controller
{
  public function addVendorLanguage(Request $request)
  {
      $value= $request->data;
      $x= json_decode($value);
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
  public function changeUserStatus(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'role' => 'required',
       'user_id'=>'required',
       'id'=>'required',
       'status'=>'required',
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
     $role=$request->role;
      if($role=='vendor')
      {
        $status=Vendor::where('id',$request->id)->update([
          'status'=>$request->status
        ]);
      }
      if($role=="seller")
      {
        $status=Seller::where('id',$request->id)->update([
          'status'=>$request->status
        ]);
      }
      // else {
      //   return response(array(
      //     'success'=>0,
      //     'msg'=>'Something Went Wrong'
      //   ),Response::HTTP_UNAUTHORIZED);
      // }
      if($status)
      {
        return response(array(
          'success'=>1,
          'data'=>[],
          'msg'=>'Status Updated Successfully'
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
   public function addVendorService(Request $request)
   {
     $validator=Validator::make($request->all(), [
        'user_id' => 'required',
        'parent_id'=>'required',
        'service_name'=>'required',
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
        $user_check=User::where(['id'=>$request->user_id,'user_role'=>'a'])->count();
        if($user_check)
        {
          $data=new VendorServiceList();
          $data->service_id=uniqid();
          $data->parent_id=$request->parent_id;
          $data->service_name=$request->service_name;
          $data->save();
          if($data)
          {
            return response(array(
              'success'=>1,
              'data'=>[],
              'msg'=>'Service Added Successfully'
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
        else
        {
          return response(array(
            'success'=>0,
            'msg'=>'User does not exist'
          ),Response::HTTP_UNAUTHORIZED);
        }
    }
   }
   public function changeStatus(Request $request){
     $validator=Validator::make($request->all(), [
        'role' => 'required',
        'user_id'=>'required',
        'id'=>'required',
        'status'=>'required',
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
        $status=$request->status;
        $id =$request->id;
        $role=$request->role;
        $status_check='';
        if($role=='userlist')
        {
          $status_check=User::where('id',$id)->update(['status'=>$status]);
        }
        else if($role=='sellerlist')
        {
          $status_check=Seller::where('id',$id)->update(['status'=>$status]);
        }
        else if($role=='vendorlist')
        {
          $status_check=Vendor::where('id',$id)->update(['status'=>$status]);
        }
        else if($role=='sellerimage')
        {
          $status_check=ImageList::where('id',$id)->update(['status'=>$status]);
        }
        else if($role=='sellervideo')
        {
          $status_check=VideoList::where('id',$id)->update(['status'=>$status]);
        }
        else if($role=='vendorservicelist')
        {
          $status_check=VendorServiceList::where('id',$id)->update(['status'=>$status]);
        }
        else if($role=='vendorgiglist')
        {
          $status_check=VendorServiceGig::where('id',$id)->update(['status'=>$status]);
        }
        else {
          return response(array(
            'success'=>0,
            'msg'=>'Something Went Wrong'
          ),Response::HTTP_UNAUTHORIZED);
        }
        if($status_check)
        {
          return response(array(
            'success'=>1,
            'data'=>[],
            'msg'=>'Status Updated Successfully'
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
  public function getHomeData()
  {
     //$service_list=VendorServiceList::where(['parent_id'=>0,'status'=>1])->with(array('getGigsList'=>function($query){
                    //$query->select("id","gig_title");
             //}))->get();
    $service_list=VendorServiceList::where(['parent_id'=>0,'status'=>1])->with('getGigsList')->get();
   // $d=json_decode($service_list,true);
   // return $d[0]['get_gigs_list'][0];
   // $s=$service_list[0]->toArray();
   $video_gallery_list=VideoList::select('id','price','video_length','short_desc','title','artist_name','large_thumb')->where('status',1)->take(16)->get();
   $image_gallery_list=ImageList::select('id','price','title','artist_name','short_desc','large_thumb')->where('status',1)->take(16)->get();
   $today_deal=VideoList::select('id','price','video_length','title','description','artist_name','large_thumb')->take(5)->get();

   $image_cat =ImageCategoryResource::collection(ImageCategory::where('status',1)->get());
   $video_cat =VideoCategoryResource::collection(VideoCategory::where('status',1)->get());

   $video_list=array();
   $image_list=array();
   $cat_list=array();
   $gallery_list[0]['name']='Video Gallery';
   $gallery_list[0]['cat_id']=5;
   $gallery_list[0]['cat_name']='video';
   $gallery_list[0]['listing']=$video_gallery_list;
   $gallery_list[1]['name']='Image Gallery';
   $gallery_list[1]['cat_id']=6;
   $gallery_list[0]['cat_name']='image';
   $gallery_list[1]['listing']=$image_gallery_list;
   $cat_list[0]['name']='Image Categories';
   $cat_list[0]['cat_id']='image_cat';
   $cat_list[0]['listing']=$image_cat;
   $cat_list[1]['name']='Video Categories';
   $cat_list[1]['cat_id']='video_cat';
   $cat_list[1]['listing']=$video_cat;
   // return($video_list[0]['listing']);
    if($service_list)
    {
      return response(array(
        'success'=>1,
        'services'=>$service_list,
        'gallery_list'=>$gallery_list,
        'category_list'=>$cat_list,
      ),Response::HTTP_OK);
    }
    else {
      return response(array(
        'success'=>0,
        'msg'=>'Something Went Wrong'
      ),Response::HTTP_UNAUTHORIZED);
    }
  }
  public function getViewMoreData(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'view_more_type' => 'required',
       'user_id'=>'required',
       'cat_id'=>'required',
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
     $view_more_type=$request->view_more_type;
     $cat_id=$request->cat_id;
     $data=array();
     $title="";
     $description="";
    if(($view_more_type=="dubbing" && $cat_id==1) || ($view_more_type=="subtitling" && $cat_id==2) || ($view_more_type=="writingtranslation" && $cat_id==3))
    {
      $data=VendorServiceGig::select('id',"vendor_service_gigs_id",'vendor_name','category_id','gig_title','language','currency','gig_description','gig_image_1','gig_image_2','gig_image_3','gig_video_url','basic_pack_price')->where(['category_id'=>$cat_id,'status'=>1])->get();
      if($view_more_type=="dubbing")
      {
        $title="Get The Best Dubbing Services";
        $description="Find the best Dubbing services you need to help you successfully meet your project planning goals and deadline";
      }
      if($view_more_type=="subtitling")
      {
        $title="Get The Best Subtitling Services";
        $description="Find the best Subtitling services you need to help you successfully meet your project planning goals and deadline";
      }
      if($view_more_type=="writingtranslation")
      {
        $title="Get The Best Writing Services";
        $description="Find the best Writing services you need to help you successfully meet your project planning goals and deadline";
      }
    }
    else if ($view_more_type=="imagegallery")
    {
      $data=ImageList::select('id','large_thumb as large_img','title','artist_name','price','short_desc')->where(['status'=>1])->get();
      $title="Get The Best Image Content";
      $description="Find the best  Image content you need to help you successfully meet your project planning goals and deadline";
    }
    else if ($view_more_type=="videogallery")
    {
        $data=VideoList::select('id','large_thumb as large_img','title','artist_name','price','short_desc')->where(['status'=>1])->get();
        $title="Get The Best Video Content";
        $description="Find the best Video Content you need to help you successfully meet your project planning goals and deadline";
    }
    else if ($view_more_type=="imagecat")
    {
      $data=ImageCategory::select('id','name','small_img','medium_img','large_img')->where(['status'=>1])->get();
      $title="Get The Best Image Content";
      $description="Find the best Image content you need to help you successfully meet your project planning goals and deadline";
    }
    else if ($view_more_type=="videocat")
    {
      $data=VideoCategory::select('id','name','small_img','medium_img','large_img')->where(['status'=>1])->get();
      $title="Get The Top Video  Content";
      $description="Find the best Video content you need to help you successfully meet your project planning goals and deadline";
    }
    else {
      return response(array(
        'success'=>0,
        'msg'=>'Something Went Wrong'
      ),Response::HTTP_UNAUTHORIZED);
    }
    if($data)
    {
      return response(array(
        'success'=>1,
        'data'=>$data,
        'title'=>$title,
        'description'=>$description
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
  public function getGigInfo(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'gig_id' => 'required',
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
     $gig_info=VendorServiceGig::where(['id'=>$request->gig_id,'status'=>1])->first();
     $vendor_info=Vendor::select('fname','lname','profile_pic','description')->where(['id'=>$gig_info->vendor_id])->first();
     $current_vendor_service_list=VendorServiceGig::select('id','vendor_service_gigs_id','vendor_name','category_id','gig_title','language','currency','gig_image_1','gig_image_2','gig_image_3','gig_video_url','basic_pack_price')->where(['vendor_id'=>$gig_info->vendor_id,'status'=>1])->get();
     $gig_list;
       $gig_list[0]['url']=$gig_info->gig_image_1;
       $gig_list[1]['url']=$gig_info->gig_image_2;
       $gig_list[2]['url']=$gig_info->gig_image_3;
       $gig_list[3]['url']=$gig_info->gig_video_url;
       $gig_info->thumb = $gig_list;
     if($gig_info && $vendor_info)
     {
       return response(array(
         'success'=>1,
         'gig_info'=>$gig_info->makeHidden(['created_at','category_id','subcategory_id','updated_at']),
         'vendor_info'=>$vendor_info,
         'more_service_by_c_vendor'=>$current_vendor_service_list
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
  public function getImageData(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'image_id' => 'required',
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
     $data=ImageList::where(['id'=>$request->image_id,'status'=>1])->first();
     $key=$data->keywords;
     // ->whereRaw("find_in_set($data->keywords,keywords)")
     $similar_images=ImageList::where('id','!=',$request->image_id)->where('status',1)->where(function($query) use($data){
       $query->orWhere('artist_name',$data->artist_name)->orWhere('image_category_id',$data->image_category_id);
     })->get();
     $cart_status=Cart::where(['item_id'=>$request->image_id,'user_id'=>$request->user_id,'item_type'=>'image'])->count();
     $new_cart_status=BuyerOttOrders::where(['item_id'=>$request->image_id,'user_id'=>$request->user_id,'item_type'=>'image','status'=>1])->count();
     if($new_cart_status)
     {
       $cart_status=2;
     }
     if($data)
     {
       return response(array(
         'success'=>1,
         'data'=>$data->makeHidden(['file_name','created_at','status','license-rights','total_buy','updated_at','territory_rights','term_condition','status','small_thumb','medium_thumb']),
         'similar_data'=>$similar_images->makeHidden(['file_name','created_at','status','license-rights','total_buy','updated_at','territory_rights','term_condition','status','small_thumb','medium_thumb']),
         'cart_status'=>$cart_status
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
  public function getVideoData(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'video_id' => 'required',
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
     $data=VideoList::where(['id'=>$request->video_id,'status'=>1])->first();
     $key=$data->keywords;
     // ->whereRaw("find_in_set($data->keywords,keywords)")
     $similar_videos=VideoList::where('id','!=',$request->video_id)->where('status',1)->where(function($query) use($data){
       $query->orWhere('artist_name',$data->artist_name)->orWhere('video_category_id',$data->video_category_id);
     })->get();
     $cart_status=Cart::where(['item_id'=>$request->video_id,'user_id'=>$request->user_id,'item_type'=>'video'])->count();
     $new_cart_status=BuyerOttOrders::where(['item_id'=>$request->video_id,'user_id'=>$request->user_id,'item_type'=>'video','status'=>1])->count();
     if($new_cart_status)
     {
       $cart_status=2;
     }
     if($data)
     {
       return response(array(
         'success'=>1,
         'data'=>$data->makeHidden(['file_name','created_at','download_url','status','license-rights','total_buy','updated_at','territory_rights','term_condition','status','small_thumb','medium_thumb']),
         'similar_data'=>$similar_videos->makeHidden(['file_name','download_url','created_at','status','license-rights','total_buy','updated_at','territory_rights','term_condition','status','small_thumb','medium_thumb']),
         'cart_status'=>$cart_status
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
  public function getLanguageList()
  {
      $lang=VendorLanguage::select('id as value','lang_name as label')->get();
      if($lang)
      {
        return response(array(
          'success'=>1,
          'data'=>$lang
        ),Response::HTTP_OK);
      }
      else {
        return response(array(
          'success'=>0,
          'data'=>[]
        ),Response::HTTP_OK);
      }
  }
  //-----------------FORM STATUS------------------//
    public function formStatus(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'view_id' => 'required',
         'view_type' => 'required',
         'user_id'=>'required',
         'seller_id'=>'required',
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
       $view_type=$request->view_type;
       $view_id=$request->view_id;
       $count=0;
       if($view_type=="video")
       {
         $count=VideoList::where('id',$view_id)->count();
       }
       else if($view_type=="image")
       {
         $count=ImageList::where('id',$view_id)->count();
       }
       else {
         return response(array(
           'success'=>0,
           'msg'=>'something went wrong'
         ));
       }
       if($count==1)
       {
         return response(array(
           'success'=>1,
           'msg'=>'Valid Id'
         ));
       }
       else {
         return response(array(
           'success'=>0,
           'msg'=>'Invalid Id'
         ));
       }
     }
    }
  //-----------END FORM STTAUS--------------------//
public function getSellerFormData()
{
  $lang=VendorLanguage::select('id as value','lang_name as label')->get();
  $country=DB::table('countries')->select('id as value','name as label')->get();
  $video_category=VideoCategory::select('id','name')->get();
  $image_category=ImageCategory::select('id','name')->get();
  if($lang || $country || $video_category || $image_category)
  {
    return response(array(
      'success'=>1,
      'language_list'=>$lang,
      'video_cat_list'=>$video_category,
      'image_cat_list'=>$image_category,
      'country_list'=>$country,
    ),Response::HTTP_OK);
  }
  else {
    return response(array(
      'success'=>0,
      'data'=>[]
    ),Response::HTTP_OK);
  }
}

  //--------SELLER FORM REQUIREMENTS DATA-------------//

  //-------END----------------------------------------//
  public function test1()
  {
    dd(config('constant.url1'));
  }
}
