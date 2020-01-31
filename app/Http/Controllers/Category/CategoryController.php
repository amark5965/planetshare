<?php

namespace App\Http\Controllers\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image_flav;
use Validator;
use App\ImageCategory;
use App\ImageList;
use App\VideoList;
use App\VideoCategory;
use App\Language;
use Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Category\ImageCategoryResource;
use App\Http\Resources\Category\VideoCategoryResource;
use App\Http\Resources\Category\LanguageResource;
class CategoryController extends Controller
{
  public function addCategory(Request $request)
  {
     $validator=Validator::make($request->all(), [
        'name' => 'required',
        'user_id'=>'required',
        'tag'=>'required',
        'desc' => 'required',
        'icon' => 'required',
    ]);
    if ($validator->fails())
    {
        return response(array(
          'success'=>0,
          'data'=>$validator->errors()
        ));
    }
    else {
      if($request->hasFile('image'))
      {
            $file= $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension
            $fileName .= "_" . md5(time()) . "." . $extension;
            $mime = str_replace('/', '-', $file->getMimeType());
            $dateFolder = date("d-m-y");
            $main_url=config('constant.url');
            $small_host_url ="{$main_url}/image/cat_img/small/{$dateFolder}/{$mime}/$fileName";
            $medium_host_url ="{$main_url}/image/cat_img/medium/{$dateFolder}/{$mime}/$fileName";
            $large_host_url ="{$main_url}/image/cat_img/large/{$dateFolder}/{$mime}/$fileName";
            $smallimage = Image_flav::make($file);
            $smallimage->resize(200,350);
            $mediumimage = Image_flav::make($file);
            $mediumimage->resize(600,400,function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
            });
            $largeimage = Image_flav::make($file);
            $path = Storage::put("/public/image/cat_img/small/{$dateFolder}/{$mime}/$fileName", (string) $smallimage->encode());
            $path1 = Storage::put("/public/image/cat_img/medium/{$dateFolder}/{$mime}/$fileName", (string) $mediumimage->encode());
            $path2 = Storage::put("/public/image/cat_img/large/{$dateFolder}/{$mime}/$fileName", (string) $largeimage->encode());
            if($request->tag=='image')
            {
              $image_cat = new ImageCategory();
            }
            if($request->tag=="video")
            {
                $image_cat = new VideoCategory();
            }
            if($request->tag=="language")
            {
                $image_cat = new Language();
            }
            else {
              response(array(
                'success'=>0,
                'data'=>[]
              ));
            }
            $image_cat->requestid = uniqid();
            $image_cat->name =$request->name;
            $image_cat->icon =$request->icon;
            $image_cat->desc=$request->desc;
            $image_cat->small_img=$small_host_url;
            $image_cat->medium_img=$medium_host_url;
            $image_cat->large_img=$large_host_url;
            $image_cat->save();
            if($image_cat)
            {
              return response(array(
                'success'=>1,
                'data'=>[],
                'msg'=>'Category Added'
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
        else {
          return response(array(
            'success'=>0,
            'data'=>[],
            'msg'=>'File is missing'
          ),Response::HTTP_UNAUTHORIZED);
        }
    }
  }
  public function getCategory(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'tag' => 'required',
       'user_id'=>'required'
     ]);
     if ($validator->fails())
     {
         return response(array(
           'success'=>0,
           'data'=>$validator->errors()
         ));
     }
     else {
       $page_limit = $request->page_limit;
       $searchKeyword = $request->searchKeyword;
       if(is_null($page_limit))
         {
           $page_limit=10;
         }
         $table_name="";
         if($request->tag=='image')
         {
           $data =ImageCategoryResource::collection(ImageCategory::where(function($query) use ($searchKeyword){
             $query->orWhere('name','like','%'.$searchKeyword.'%')->orWhere('icon','like','%'.$searchKeyword.'%')->
             orWhere('desc','like','%'.$searchKeyword.'%')->orWhere('requestid','like','%'.$searchKeyword.'%');
           })->paginate($page_limit));
         }
         if($request->tag=="video")
         {
           $data =VideoCategoryResource::collection(VideoCategory::where(function($query) use ($searchKeyword){
             $query->orWhere('name','like','%'.$searchKeyword.'%')->orWhere('icon','like','%'.$searchKeyword.'%')->
             orWhere('desc','like','%'.$searchKeyword.'%')->orWhere('requestid','like','%'.$searchKeyword.'%');
           })->paginate($page_limit));
         }
         if($request->tag=="language")
         {
           $data =LanguageResource::collection(Language::where(function($query) use ($searchKeyword){
             $query->orWhere('name','like','%'.$searchKeyword.'%')->orWhere('icon','like','%'.$searchKeyword.'%')->
             orWhere('desc','like','%'.$searchKeyword.'%')->orWhere('requestid','like','%'.$searchKeyword.'%');
           })->paginate($page_limit));
         }
         else {
           response(array(
             'success'=>0,
             'data'=>[],
             'msg'=>'Something Went Wrong'
           ),Response::HTTP_UNAUTHORIZED);
         }
         if($data)
         {
           return response(array(
             'success'=>1,
             'data'=>$data
           ),Response::HTTP_OK);
         }
         else {
           response(array(
             'success'=>0,
             'data'=>[],
             'msg'=>'Something Went Wrong'
           ),Response::HTTP_UNAUTHORIZED);
         }
     }
  }
  public function getImageCategoryData(Request $request)
  {
      $validator=Validator::make($request->all(), [
         'tag' => 'required',
         'user_id'=>'required',
         'category_id'=>'required'
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
          $limit=20;
          $keyword='';
          if(!is_null($request->limit))
          {
            $limit=$request->limit;
          }
          if(!is_null($request->keyword))
          {
            $keyword=$request->keyword;
          }
          $data=ImageList::select('id','request_id','short_desc','image_category_id','title','small_thumb','medium_thumb','large_thumb','dimension','extension','keywords','artist_name','price')->where(['image_category_id'=>$request->category_id,'status'=>1])->orWhere(function($query) use ($keyword){
               $query->orWhere('artist_name','=',$keyword);
               })->paginate($limit);
         if($data)
         {
           return response(array(
             'success'=>1,
             'data'=>$data,
             'total_result'=>$data->total()
           ),Response::HTTP_OK);
         }
         else {
           response(array(
             'success'=>0,
             'data'=>[],
             'msg'=>'Something Went Wrong'
           ),Response::HTTP_UNAUTHORIZED);
         }
      }
    }
  public function getVideoCategoryData(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'tag' => 'required',
       'user_id'=>'required',
       'category_id'=>'required'
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
        $limit=20;
        $keyword='';
        if(!is_null($request->limit))
        {
          $limit=$request->limit;
        }
        if(!is_null($request->keyword))
        {
          $keyword=$request->keyword;
        }
       $data=VideoList::select('id','request_id','title','short_desc','video_category_id','demo_url','small_thumb','medium_thumb','large_thumb','dimension','extension','keywords','artist_name','price','cast','producer','description')->where(['video_category_id'=>$request->category_id,'status'=>1])->orWhere(function($query) use ($keyword){
            $query->orWhere('artist_name','=',$keyword);
          })->paginate($limit);
       if($data)
       {
         // ->makeHidden(['created_at','updated_at','license_rights','file_name','territory_rights','download_url','term_condition'])
         return response(array(
           'success'=>1,
           'data'=>$data,
           'total_result'=>$data->total()
         ),Response::HTTP_OK);
       }
       else {
         response(array(
           'success'=>0,
           'data'=>[],
           'msg'=>'Something Went Wrong'
         ),Response::HTTP_UNAUTHORIZED);
       }
    }
  }
  public function ImageFilterContent(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'tag' => 'required',
       'user_id'=>'required',
       'category_id'=>'required'
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
          $data=ImageList::select('id','artist_name')->where(['image_category_id'=>$request->category_id,'status'=>1])->get();
          if($data)
          {
            return response(array(
              'success'=>1,
              'data'=>$data,
            ),Response::HTTP_OK);
          }
          else {
            response(array(
              'success'=>0,
              'data'=>[],
              'msg'=>'Something Went Wrong'
            ),Response::HTTP_UNAUTHORIZED);
          }

      }
  }
  public function VideoFilterContent(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'tag' => 'required',
       'user_id'=>'required',
       'category_id'=>'required'
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
        $data=VideoList::select('id','artist_name')->where(['video_category_id'=>$request->category_id,'status'=>1])->get();
        if($data)
        {
          return response(array(
            'success'=>1,
            'data'=>$data,
          ),Response::HTTP_OK);
        }
        else {
          response(array(
            'success'=>0,
            'data'=>[],
            'msg'=>'Something Went Wrong'
          ),Response::HTTP_UNAUTHORIZED);
        }

      }
  }
}
