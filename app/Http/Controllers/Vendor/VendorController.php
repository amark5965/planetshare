<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Auth;
use App\Vendor;
use App\VendorServiceList;
use App\VendorServiceGig;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\VendorServiceListResource;
class VendorController extends Controller
{
  public function createVendor(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id' => 'required',
       'fname'=>'required',
       'lname'=>'required',
       'file'=>'required',
       'description'=>'required',
       'terms_condition'=>'required',
       'company_info'=>'required',
       'country'=>'required',
       'address'=>'required',
       'state'=>'required',
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
     $file= $request->file;
     $extension = $file->getClientOriginalExtension();
     $fileName = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension
     $fileName .= "_" . md5(time()) . "." . $extension;
     $mime = str_replace('/', '-', $file->getMimeType());
     $dateFolder = date("d-m-y");
     $main_url=config('constant.url');
     $profile_pic ="{$main_url}/image/vendor/profile_pic/{$dateFolder}/{$mime}/$fileName";
     $filePath_image = "public/image/vendor/profile_pic/{$dateFolder}/{$mime}";
     $file->storeAs($filePath_image, $fileName);
      $user_id=$request->user_id;
      $created_by=$request->tag;
      $fname=$request->fname;
      $lname=$request->lname;
      $description=$request->description;
      $personal_website='';
      if(!is_null($request->personal_website))
      {
        $personal_website=$request->personal_website;
      }
      $terms_condition=$request->terms_condition;
      $company_info=$request->company_info;
      $country=$request->country;
      $state=$request->state;
      $address=$request->address;
      $city=$request->city;
      $pincode=$request->pincode;
      $vendor=new Vendor();
      $vendor->Accountid=uniqid();
      $vendor->user_id=$user_id;
      $vendor->fname=$fname;
      $vendor->lname=$lname;
      $vendor->profile_pic=$profile_pic;
      $vendor->description=nl2br($description);
      $vendor->personal_website=$personal_website;
      $vendor->company_info=$company_info;
      $vendor->address=$address;
      $vendor->city=$city;
      $vendor->state=$state;
      $vendor->country=$country;
      $vendor->pincode=$pincode;
      $vendor->created_by=$created_by;
      $vendor->term_condition=$terms_condition;
      $vendor->save();
      if($vendor)
      {
        return response(array(
          'success'=>1,
          'vendor_id'=>$vendor->id,
          'msg'=>'Successfully Created'
        ),Response::HTTP_OK);
      }
      else {
        return response(array(
          'success'=>0,
          'data'=>[],
          'msg'=>'Something Went Wrong'
        ),Response::HTTP_OK);
      }
   }
  }
  public function addVendorBankDetails(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id' => 'required',
       'vendor_id'=>'required',
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
     $vendor=Vendor::where('id',$request->vendor_id)->update([
       'account_holder_name'=>$request->account_holder_name,
       'ifsc_code'=>$request->ifsc_code,
       'account_number'=>$request->account_number,
       'bank_name'=>$request->bank_name,
     ]);
     if($vendor)
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
  public function getCategoryAndSubcategory(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id' => 'required',
       'vendor_id'=>'required',
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
     $category_list=VendorServiceListResource::collection(VendorServiceList::with('children')->where('parent_id',0)->orderBy('service_name', 'asc')->get());
     if($category_list)
     {
       return response(array(
         'success'=>1,
         'data'=>$category_list
       ),Response::HTTP_OK);
     }
     else
     {
       return response(array(
         'success'=>0,
         'data'=>[],
         'msg'=>'Something Went Wrong'
       ),Response::HTTP_UNAUTHORIZED);
     }
   }
  }
  public function createVendorGig(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id' => 'required',
       'vendor_id'=>'required',
       'tag'=>'required',
       'gig_title'=>'required',
       'category_id'=>'required',
       'subcategory_id'=>'required',
       'language'=>'required',
       'search_tag'=>'required',
       'basic_pack_name'=>'required',
       'basic_pack_description'=>'required',
       'basic_pack_price'=>'required',
       'basic_pack_revision'=>'required',
       'basic_pack_delivery_time'=>'required',
       'standard_pack_name'=>'required',
       'standard_pack_description'=>'required',
       'standard_pack_price'=>'required',
       'standard_pack_revision'=>'required',
       'standard_pack_delivery_time'=>'required',
       'premium_pack_name'=>'required',
       'premium_pack_description'=>'required',
       'premium_pack_price'=>'required',
       'premium_pack_revision'=>'required',
       'premium_pack_delivery_time'=>'required',
       'gig_description'=>'required',
       'gig_image_1'=>'required',
       'gig_image_2'=>'required',
       'gig_image_3'=>'required',
       'gig_video_url'=>'required',
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
     $getRegGig=VendorServiceGig::where(['category_id'=>$request->category_id,'subcategory_id'=>$request->subcategory_id])->count();
     if($getRegGig==1)
     {
       return response(array(
         'success'=>2,
         'msg'=>'Above Category Gig has been declared already'
       ));
     }
     else {
     $vendor_name=Vendor::where('id',$request->vendor_id)->first();
     $gig_image_1=$request->gig_image_1;
     $gig_image_2=$request->gig_image_2;
     $gig_image_3=$request->gig_image_3;
     $gig_image_1_fileName = $this->createFilename($gig_image_1);
     $gig_image_2_fileName = $this->createFilename($gig_image_2);
     $gig_image_3_fileName = $this->createFilename($gig_image_3);
     $gig_image_1_mime = str_replace('/', '-', $gig_image_1->getMimeType());
     $gig_image_2_mime = str_replace('/', '-', $gig_image_2->getMimeType());
     $gig_image_3_mime = str_replace('/', '-', $gig_image_3->getMimeType());
     $dateFolder = date("d-m-y");
     $gig_image= "public/image/vendor/vendor_gigs/{$dateFolder}";
     $gig_image_1->storeAs("{$gig_image}/{$gig_image_1_mime}", $gig_image_1_fileName);
     $gig_image_2->storeAs("{$gig_image}/{$gig_image_2_mime}", $gig_image_2_fileName);
     $gig_image_3->storeAs("{$gig_image}/{$gig_image_3_mime}", $gig_image_3_fileName);
    $main_url=config('constant.url');
     $gig_image_1_url="{$main_url}/image/vendor/vendor_gigs/{$dateFolder}/{$gig_image_1_mime}/{$gig_image_1_fileName}";
     $gig_image_2_url="{$main_url}/image/vendor/vendor_gigs/{$dateFolder}/{$gig_image_2_mime}/{$gig_image_2_fileName}";
     $gig_image_3_url="{$main_url}/image/vendor/vendor_gigs/{$dateFolder}/{$gig_image_3_mime}/{$gig_image_3_fileName}";
     $gig_video_url=$request->gig_video_url;
     $gig_video_fileName = $this->createFilename($gig_video_url);
     $video_gig_mime = str_replace('/', '-', $gig_video_url->getMimeType());
     $filePath_video = "public/video/vendor/vendor_gigs/{$dateFolder}/{$video_gig_mime}";
     $gig_video_url->storeAs($filePath_video, $gig_video_fileName);
     $gig_video_final_url="{$main_url}/video/vendor/vendor_gigs/{$dateFolder}/{$video_gig_mime}/{$gig_video_fileName}";
     $vendor_f_name="{$vendor_name->fname} {$vendor_name->lname}";
     $vendor_service=new VendorServiceGig();
     $vendor_service->vendor_service_gigs_id=uniqid();
     $vendor_service->vendor_id=$request->vendor_id;
     $vendor_service->vendor_name=$vendor_f_name;
     $vendor_service->gig_title=$request->gig_title;
     $vendor_service->gig_description=nl2br($request->gig_description);
     $vendor_service->category_id=$request->category_id;
     $vendor_service->subcategory_id=$request->subcategory_id;
     $vendor_service->language=$request->language;
     $vendor_service->search_tag=$request->search_tag;
     $vendor_service->basic_pack_name=$request->basic_pack_name;
     $vendor_service->basic_pack_description=$request->basic_pack_description;
     $vendor_service->basic_pack_price=$request->basic_pack_price;
     $vendor_service->basic_pack_revision=$request->basic_pack_revision;
     $vendor_service->basic_pack_delivery_time=$request->basic_pack_delivery_time;
     $vendor_service->standard_pack_name=$request->standard_pack_name;
     $vendor_service->standard_pack_description=$request->standard_pack_description;
     $vendor_service->standard_pack_price=$request->standard_pack_price;
     $vendor_service->standard_pack_revision=$request->standard_pack_revision;
     $vendor_service->standard_pack_delivery_time=$request->standard_pack_delivery_time;
     $vendor_service->premium_pack_name=$request->premium_pack_name;
     $vendor_service->premium_pack_description=$request->premium_pack_description;
     $vendor_service->premium_pack_price=$request->premium_pack_price;
     $vendor_service->premium_pack_revision=$request->premium_pack_revision;
     $vendor_service->premium_pack_delivery_time=$request->premium_pack_delivery_time;
     $vendor_service->gig_image_1=$gig_image_1_url;
     $vendor_service->gig_image_2=$gig_image_2_url;
     $vendor_service->gig_image_3=$gig_image_3_url;
     $vendor_service->gig_video_url=$gig_video_final_url;
     if($request->category_id==1 || $request->category_id==2)
     {
       $vendor_service->basic_pack_video_length=$request->basic_pack_video_length;
       $vendor_service->standard_pack_video_length=$request->standard_pack_video_length;
       $vendor_service->premium_pack_video_length=$request->premium_pack_video_length;
     }
     if($request->category_id==3)
     {
       $vendor_service->basic_pack_closed_captioning=$request->basic_pack_closed_captioning;
       $vendor_service->standard_pack_closed_captioning=$request->standard_pack_closed_captioning;
       $vendor_service->premium_pack_closed_captioning=$request->premium_pack_closed_captioning;
       $vendor_service->basic_pack_word_count=$request->basic_pack_word_count;
       $vendor_service->standard_pack_word_count=$request->standard_pack_word_count;
       $vendor_service->premium_pack_word_count=$request->premium_pack_word_count;
     }
     $vendor_service->save();
     if($vendor_service)
     {
       return response(array(
         'success'=>1,
         'data'=>[],
         'msg'=>'Your Service added Successfully'
       ),Response::HTTP_OK);
     }
     else
     {
       return response(array(
         'success'=>0,
         'data'=>[],
         'msg'=>'Something Went Wrong'
       ),Response::HTTP_UNAUTHORIZED);
     }
   }
 }
  }
  public function createFilename($file)
  {
      $extension = $file->getClientOriginalExtension();
      $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

      // Add timestamp hash to name of the file
      $filename .= "_" . md5(time()) . "." . $extension;

      return $filename;
  }
  //--------------GET VENDOR LIST---------------//
  public function getVendorList(Request $request)
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
      $vendorlist=Vendor::paginate($limit);
      if($vendorlist)
      {
        return response(array(
          'success'=>1,
          'data'=>$vendorlist,
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
//---------------END--------------------------//
//------------GET VENDOR GIGS------------------//
public function getVendorGigsList(Request $request)
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
   $vendorgiglist='';
   if(!is_null($request->vendor_id))
   {
        $vendorgiglist=VendorServiceGig::where('vendor_id',$request->vendor_id)->orderBy('created_at','desc')->paginate($limit);
   }
   else
   {
     $vendorgiglist=VendorServiceGig::orderBy('created_at','desc')->paginate($limit);
   }
    if($vendorgiglist)
    {
      return response(array(
        'success'=>1,
        'data'=>$vendorgiglist,
        'msg'=>'Gig List'
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
//--------------END----------------------------//
//--------------GET VENDOR SERVICE LIST--------//
public function getVendorServiceList(Request $request)
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
    $vendorlist=VendorServiceList::paginate($limit);
    if($vendorlist)
    {
      return response(array(
        'success'=>1,
        'data'=>$vendorlist,
        'msg'=>'Vendor Category List'
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
//---------------END------------------------//

//-------------GET VENDOR GIGS TABS--------------//
  public function getVendorTabContent(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id' => 'required',
       'vendor_id' => 'required',
       'tab_index'=>'required',
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
     $active=VendorServiceGig::where(['status'=>1,'vendor_id'=>$request->vendor_id])->count();
     $inactive=VendorServiceGig::where(['status'=>0,'vendor_id'=>$request->vendor_id])->count();
     $pending=VendorServiceGig::where(['status'=>2,'vendor_id'=>$request->vendor_id])->count();
     $disapproved=VendorServiceGig::where(['status'=>3,'vendor_id'=>$request->vendor_id])->count();
     $tabs=array();
     $tabs[0]['name']='ACTIVE';
     $tabs[0]['count']=$active;
     $tabs[0]['id']=1;
     $tabs[1]['name']='PENDING APPROVAL';
     $tabs[1]['count']=$pending;
     $tabs[1]['id']=2;
     $tabs[2]['name']='DENIED';
     $tabs[2]['count']=$disapproved;
     $tabs[2]['id']=3;
     $tabs[3]['name']='PAUSED';
     $tabs[3]['count']=$inactive;
     $tabs[3]['id']=0;
     $limit=10;
     if(!is_null($limit))
     {
       $limit=$request->limit;
     }
     $tab_data=VendorServiceGig::where(['status'=>$request->tab_index,'vendor_id'=>$request->vendor_id])->orderBy('created_at','desc')->paginate($limit);
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
//----------------------END----------------------//

//------------------------CHANGE VENDOR GIG STATUS-----------------
    public function vendorChangeStatus(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id' => 'required',
         'vendor_id' => 'required',
         'tag'=>'required',
         'gig_id'=>'required',
         'status'=>'required'
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
       $response=VendorServiceGig::where(['id'=>$request->gig_id,'vendor_id'=>$request->vendor_id])->update([
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
    //-----------------------END_------------------------
    public function vendorGigInfo(Request $request)
    {
      $validator=Validator::make($request->all(), [
         'user_id' => 'required',
         'vendor_id' => 'required',
         'tag'=>'required',
         'gig_id'=>'required',
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
       $response=VendorServiceGig::where(['id'=>$request->gig_id,'vendor_id'=>$request->vendor_id])->first();
       if($response)
       {
         return response(array(
           'success'=>1,
           'data'=>$response,
           'msg'=>'Gig Info'
         ));
       }
       else {
         return response(array(
           'success'=>0,
           'data'=>[],
           'msg'=>'Something Went Wrong'
         ));
       }
     }
    }
}
