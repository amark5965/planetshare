<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VendorServiceList;
use App\VendorServiceGig;
use App\ImageCategory;
use App\VideoCategory;
use App\ImageList;
use App\VideoList;
use App\Vendor;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Category\ImageCategoryResource;
use App\Http\Resources\Category\VideoCategoryResource;
use Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommonController extends Controller
{
    /*
    Auth:Amar
    Created:26/12/2019
    */

    //function for get home data services
    public function getHomeData()
    {
    	$service_list = array();
      $service_list=VendorServiceList::select("id", "service_name")->where(['parent_id'=>0,'status'=>1])->withCount('vendorServiceCount')->get();
      $image_cat =ImageCategory::select("id", "name", "desc", "small_img", "medium_img", "large_img")->where('status',1)->withCount('getImageCount')->get();
      $video_cat =VideoCategory::select("id", "name", "desc", "small_img", "medium_img", "large_img")->where('status',1)->withCount('getVideoCount')->get();

        for($i=0;$i<count($service_list);$i++)
        {
          $service = "service";
          $service_list[$i]['cat_name'] = $service;
        }

        for($i=0;$i<count($image_cat);$i++)
          {
             $service = "image_cat";
             $image_cat[$i]['cat_name'] = $service;
          }

        for($i=0;$i<count($video_cat);$i++)
         {
             $service = "video_cat";
             $video_cat[$i]['cat_name'] = $service;
         }

        $items_trans = [
       'id'  => '4',
       'service_name'  => 'Transcoding',
       'vendor_service_count_count' => '5',
       'cat_name'   => 'service',
       ];

        $items_stram = [
       'id'  => '5',
       'service_name'  => 'Streaming',
       'vendor_service_count_count' => '3',
       'cat_name'   => 'service',
      ];

        $items_arch = [
       'id'  => '6',
       'service_name'  => 'Archiving',
       'vendor_service_count_count' => '10',
       'cat_name'   => 'service',
        ];

        $service_list[3]= $items_trans;
        $service_list[4]= $items_stram;
        $service_list[5]= $items_arch;


      if($service_list)
        {
            return response(array(
                'success'=>1,
                'service'=>$service_list,
                'image_cat'=>$image_cat,
                'video_cat'=>$video_cat,
            ));
        }
        else {
            return response(array(
                'success'=>0,
                'msg'=>'Something Went Wrong'
            ));
          } 
          
    }

    //function for show all data
    public function viewAll(Request $request)
    {


    	$validator=Validator::make($request->all(), [
             'cat_name' => 'required',
             'user_id'=>'required|numeric',
             'cat_id'=>'required|numeric',
             'tag'=>'required'
         ]);
           
          if ($validator->fails())
          {
             return response(array(
                 'success'=>0,
                 'data'=>[$validator->errors()]
             ));
         }
        // $response = [];
        // $response['status'] = 0;
        // $response['data'] = [$validator->errors()];
        // $response['message'] = trans('messages.validation_errors');
        
        // throw new HttpResponseException(response()->json($response, 422));
      //}
         else
         {
             $limit = $request->limit;
             if(is_null($limit))
             {
                 $limit = 10;
             }
             $cat_name = $request->cat_name;
             $cat_id = $request->cat_id;
             $tag = $request->tag;
             $data=array();
             if(($cat_name=="service"))
             {

              // return $f;
              $d=VendorServiceGig::where('category_id',$cat_id)->get();
              $data=VendorServiceGig::select('id','vendor_id','vendor_name','category_id','gig_title','language','currency','gig_description','gig_image_1','gig_image_2','gig_image_3','gig_video_url','basic_pack_price')
              ->where(['category_id'=>$cat_id,'status'=>1])->paginate($limit);


             }
             else if ($cat_name=="image_cat" && $tag=="mobile")
             {
                 $data=ImageList::select('id','large_thumb as large_img','title','artist_name','price','short_desc','image_category_id')->where(['image_category_id'=>$cat_id,'status'=>1])->paginate($limit);
                 for($i=0;$i<count($data);$i++)
                 {
                     $randomNumber = rand(1,3);
                     $span =  $data[$i]['span'] = $randomNumber;
                 }
             }
             else if ($cat_name=="video_cat" && $tag=="mobile")
             {
                 $data=VideoList::select('id','large_thumb as large_img','title','artist_name','price','short_desc','video_category_id')->where(['video_category_id'=>$cat_id,'status'=>1])->paginate($limit);
             }
             else {
                 return response(array(
                     'success'=>0,
                     'msg'=>'Something Went Wrong'
                 ));
             }
                if(!$data->isEmpty())
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

    //function for giginfo
    public function giginfo(Request $request)
       {
           $validator=Validator::make($request->all(), [
               'user_id'=>'required|numeric',
               'gig_id'=>'required|numeric',
               'gig_category_id'=>'required|numeric',
               'gig_vendor_id'=>'required|numeric',
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

            $id = $request->gig_id;
            $gig_vendor_id= $request->gig_vendor_id;
            $gig_category_id = $request->gig_category_id;

             $gig_detail = VendorServiceGig::where(['id'=>$id, 'status'=>1])->first();
             $vendor_datail = Vendor::select('fname','lname','profile_pic','description')->where(['id'=>$gig_vendor_id])->first();
             $vendor_service_list = VendorServiceGig::select('id','vendor_id','vendor_name','category_id','gig_title','language','currency','gig_image_1','gig_image_2','gig_image_3','gig_video_url','basic_pack_price')->where(['vendor_id'=>$gig_vendor_id])->get();

             $data= array();
             $data[0]['name']='gig_detail';
             $data[0]['listing']=$gig_detail;

              if($gig_detail && $vendor_datail)
               {
                   return response(array(
                     'success'=>1,
                     'data'=>$data,
                     'vendor_datail'=>$vendor_datail,
                     'vendor_service_list'=>$vendor_service_list,
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
    public function video_detail(Request $request){
          $validator =  Validator::make($request->all(),[
            'tag'     =>'required',
            'user_id' =>'required',
            'cat_id'  =>'required|numeric',
            'video_category_id'=>'required|numeric',
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
               $cat_id = $request->cat_id;
               $video_category_id =$request->video_category_id;

                $data=VideoList::select('id','user_id','seller_id','seller_name','video_category_id','large_thumb','extension','video_length','keywords','price','artist_name','title','description','genre','producer','short_desc','cast','director')
                               ->where('id','=',$cat_id)
                               ->get();

               $similar_data=VideoList::select('id','large_thumb as large_img','title','artist_name','price','short_desc','video_category_id')
               ->where(['video_category_id'=>$video_category_id,'status'=>1])
               ->where('id', '!=' , $cat_id)
               ->get();

               if($data){
                 return response(array(
                     'success'=>1,
                     'data'=>$data,
                     'similar_data' =>$similar_data,
                 ),Response::HTTP_OK);
               }
               else{
                 return response(array(
                     'success'=>0,
                     'msg'=>'something gone wrong',
                 ),Response::HTTP_OK);
               }

             }
      }


      public function image_detail(Request $request){
          $validator =  Validator::make($request->all(),[
            'tag'     =>'required',
            'user_id' =>'required',
            'cat_id'  =>'required|numeric',
            'image_category_id' =>'required|numeric',
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
           $cat_id = $request->cat_id;
           $tag    = $request->tag;
           $image_category_id = $request->image_category_id;

            $data=ImageList::select('id','user_id','seller_id','seller_name','image_category_id','small_thumb','medium_thumb','large_thumb','dimension','extension','size','keywords','price','artist_name','title','description','short_desc')
                           ->where('id','=',$cat_id)
                           ->first();

              $similar_data=ImageList::select('id','large_thumb as large_img','title','artist_name','price','short_desc','image_category_id')
               ->where(['image_category_id'=>$image_category_id,'status'=>1])
               ->where('id', '!=' , $cat_id) 
               ->get();

               if($data ){
                 return response(array(
                     'success'=>1,
                     'data'=>$data,
                     'similar_data'=>$similar_data,
                 ),Response::HTTP_OK);
               }
               else{
                 return response(array(
                     'success'=>0,
                     'msg'=>'something gone wrong',
                    
                 ),Response::HTTP_OK);
               }


             }
      }

      //function for home page
      public function home_page()
      {
        $service_list = array();
        $image_cat =ImageCategory::select("id", "name", "desc", "small_img", "medium_img", "large_img")->where('status',1)->withCount('getImageCount_1')->with('getImageCount_1')->get();

        $video_cat =VideoCategory::select("id", "name", "desc", "small_img", "medium_img", "large_img")->where('status',1)->withCount('getVideoCount_1')->with('getVideoCount_1')->get();

        for($i=0;$i<count($image_cat);$i++)
        {
          $service = "image";
          $image_cat[$i]['cat_name'] = $service;
        }

        for($i=0;$i<count($video_cat);$i++)
        {
          $service = "video";
          $video_cat[$i]['cat_name'] = $service;
        }


        $items_stram = [
          'id'  => '5',
          'service_name'  => 'Streaming',
          'vendor_service_count_count' => '3',
          'cat_name'   => 'service',
        ];

        $service_list[4]= $items_stram;

        if($service_list)
        {
          return response(array(
            'success'=>1,
            'image_cat'=>$image_cat,
            'video_cat'=>$video_cat,
          ));
        }
        else
        {
          return response(array(
            'success'=>0,
            'msg'=>'Something Went Wrong'
          ));
        }
      }
      
      //function for all detail of image and video
      public function view_all(Request $request)
      {
        $dataForm = $request->all();
        $id=$request->id;
        $limit= $request->limit;
        $cat_name= $request->cat_name;
        if(is_null($limit))
        {
          $limit = 5;
        }
        $rules =[
          'tag'=>'required',
          'cat_name' =>'required|regex:/^[a-zA-Z]+$/u|max:255',
          'user_id' =>'required|numeric',
          'id' => 'required|numeric',
        ];
        $valid = validator($dataForm, $rules);
        if($valid->fails())
        {
          return response()->json([ 'success'=>'0', 'error'=>$valid->errors()->all()]);
        }
        else
        {
          $data="";
          if($cat_name =="image")
          {
            $data=ImageList::where('image_category_id',$id)->where('status',1)->paginate($limit);
            $data->makeHidden(['file_name','download_url','created_at','request_id','updated_at']);
          }
          else if ($cat_name=="video")
          {
            $data=VideoList::where('video_category_id',$id)->where('status',1)->paginate($limit);
            $data->makeHidden(['created_at','request_id','updated_at','download_url','demo_url','file_name']);
          }
          if(!$data==""  && $cat_name)
          {
            return response(array(
              'success'=>1,
              'data'=>$data,
            ));
          }
          else
          {
            return response(array(
              'success'=>0,
              'msg'=>'Something Went Wrong'
            ));
          }
        }
      }




      

    

}
