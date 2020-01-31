<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Validator;
use Auth;
use FFMpeg;
use Image_flav;
use App\User;
use App\Seller;
use App\ImageList;
use Storage;
use DB;
use Symfony\Component\HttpFoundation\Response;
class ImageUpload extends Controller
{
  public function uploadImage(Request $request) {
    $validator=Validator::make($request->all(), [
       'seller_id' => 'required',
       'user_id'=>'required',
       'file'=>'required',
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
     if($request->hasFile('file'))
     {
      $seller_info=Seller::where('id',$request->seller_id)->first();
       for($i=0;$i<count($request->file('file'));$i++)
       {
          $file=$request->file('file')[$i];
          $fileName = $this->createFilename($file);
          // Group files by mime type
          $mime = str_replace('/', '-', $file->getMimeType());
          // Group files by the date (week
          $dateFolder = date("d-m-y");

          // Build the file path
          $filePath = "public/image/{$dateFolder}/{$mime}/";
          $filePath1 = "image/{$dateFolder}/{$mime}/";
          // $thumbPath = "image/{$dateFolder}/thumbnail/";
          $finalPath = storage_path("app/".$filePath);
          //echo $filePath.$fileName;die;
          $main_url=config('constant.url');
          $base_url="{$main_url}/";
          $small_host_url ="{$base_url}image/seller/small/{$dateFolder}/{$mime}/$fileName";
          $medium_host_url ="{$base_url}image/seller/medium/{$dateFolder}/{$mime}/$fileName";
          $large_host_url ="{$base_url}image/seller/large/{$dateFolder}/{$mime}/$fileName";
          $original_host_url ="{$base_url}image/seller/original/{$dateFolder}/{$mime}/$fileName";
          $smallimage = Image_flav::make($file);
          $smallimage->resize(200,350);
          $smallimage->insert('watermark_s.png', 'bottom-right', 10, 10);
          $mediumimage = Image_flav::make($file);
          $mediumimage->resize(600,400);
          $mediumimage->insert('watermark_m.png', 'bottom-right', 10, 10);
          $largeimage = Image_flav::make($file);
          $largeimage->insert('watermark_l.png', 'bottom-right', 10, 10);
          $originalimage = Image_flav::make($file);
          $path = Storage::put("/public/image/seller/small/{$dateFolder}/{$mime}/$fileName", (string) $smallimage->encode());
          $path1 = Storage::put("/public/image/seller/medium/{$dateFolder}/{$mime}/$fileName", (string) $mediumimage->encode());
          $path2 = Storage::put("/public/image/seller/large/{$dateFolder}/{$mime}/$fileName", (string) $largeimage->encode());
          $original_path = Storage::put("/public/image/seller/original/{$dateFolder}/{$mime}/$fileName", (string) $originalimage->encode());
          $user_id=$request->user_id;
          $width=Image_flav::make($request->file('file')[$i])->width();
          $height=Image_flav::make($request->file('file')[$i])->height();
          $image_cat = new ImageList();
          $image_cat->request_id = uniqid();
          $image_cat->file_name = $fileName;
          $image_cat->user_id = $user_id;
          $image_cat->seller_id=$request->seller_id;
          $image_cat->seller_name=$seller_info->display_name;
          $image_cat->download_url=$original_host_url;
          $image_cat->small_thumb =$small_host_url;
          $image_cat->medium_thumb = $medium_host_url;
          $image_cat->large_thumb= $large_host_url;
          $image_cat->extension=$file->getClientOriginalExtension();
          $image_cat->size=$file->getSize();
          $image_cat->dimension="${width}x${height}";
          $image_cat->save();
        }
        if($image_cat)
        {
          return response(array(
            'success'=>1,
            'msg'=>'File Uploaded Successfully'
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
  }
  protected function createFilename(UploadedFile $file)
  {
      $extension = $file->getClientOriginalExtension();
      $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

      // Add timestamp hash to name of the file
      $filename .= "_" . md5(time()) . "." . $extension;

      return $filename;
  }

//---------Insert Upload Image Description------------
public function insertImageDescription(Request $request)
{
  $validator=Validator::make($request->all(), [
     'user_id'=>'required',
     'seller_id'=>'required',
     'image_id'=>'required',
     'tag'=>'required',
     'keywords'=>'required',
     'mature_content'=>'required',
     'title'=>'required',
     'artist_name'=>'required',
     'description'=>'required',
     'short_desc'=>'required',
     'territory_rights'=>'required',
     'license_rights'=>'required',
     'price'=>'required',
     'image_category_id'=>'required',
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
        $status_check=ImageList::where(['id'=>$request->image_id,'seller_id'=>$request->seller_id])->count();
        if($status_check==1)
        {
          $seller_name=Seller::where('id',$request->seller_id)->first();
          $data=ImageList::where('id',$request->image_id)->update([
          'image_category_id'=>$request->image_category_id,
          'seller_id'=>$request->seller_id,
          'seller_name'=>$seller_name->display_name,
          'title'=>$request->title,
          'artist_name'=>$request->artist_name,
          'description'=>nl2br($request->description),
          'short_desc'=>nl2br($request->short_desc),
          'territory_rights'=>$request->territory_rights,
          'license-rights'=>$request->license_rights,
          'price'=>$request->price,
          'status'=>2,
          'mature_content'=>$request->mature_content,
          'keywords'=>$request->keywords,
        ]);
        if($data)
        {
          return response(array(
            'success'=>1,
            'data'=>[],
            'msg'=>'Image Submitted Successfully'
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
          ),Response::HTTP_UNAUTHORIZED);
        }

    }
}
//------------------END-------------------------------


//----------Seller Image Data By tabs------------------
  public function getSellerImageData(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id'=>'required',
       'image_tag'=>'required',
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
        $image_tag=$request->image_tag;
        if($image_tag=='queue')
        {
          //DB::enableQueryLog();
          $image=ImageList::select('request_id','id as image_id','large_thumb','dimension','size','description','extension')->where(['user_id'=>$request->user_id,'status'=>4])->orderBy('id', 'DESC')->get();
          //dd(DB::getQueryLog());
        }
        else if($image_tag=='pending')
        {
            $image=ImageList::select('request_id','id as image_id','large_thumb','dimension','size','extension')->where(['user_id'=>$request->user_id,'status'=>2])->orderBy('id', 'DESC')->get();
        }
        else if($image_tag=='review')
        {
            $image=ImageList::where('user_id',$request->user_id)->where('status',1)->orWhere('status',3)->orderBy('id', 'DESC')->get();
        }
        if(!empty($image))
        {
          return response(array(
            'success'=>1,
            'data'=>$image
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
//---------------END---------------------------
}
