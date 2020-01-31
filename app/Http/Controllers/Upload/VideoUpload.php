<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Validator;
use Auth;
use App\User;
use App\VideoList;
use FFMpeg;
use Storage;
use App\Seller;
use Image_flav;
use Symfony\Component\HttpFoundation\Response;
class VideoUpload extends Controller
{
  public function uploadVideo(Request $request) {
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
      // create the file receiver
      $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

      // check if the upload is success, throw exception or return response you need
      if ($receiver->isUploaded() === false) {
          throw new UploadMissingFileException();
      }

      // receive the file
      $save = $receiver->receive();

      // check if the upload has finished (in chunk mode it will send smaller files)
      if ($save->isFinished()) {
          // save the file and return any response you need, current example uses `move` function. If you are
          // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
          return $this->saveFile($save->getFile(),$request);
      }

      // we are in chunk mode, lets send the current progress
      /** @var AbstractHandler $handler */
      $handler = $save->handler();
      sleep(1);
      return response()->json([
          "done" => $handler->getPercentageDone(),
          'status' => true
      ]);
    }
  }

  /**
   * Saves the file
   *
   * @param UploadedFile $file
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function saveFile(UploadedFile $file,$request)
  {
    $seller_info=Seller::where('id',$request->seller_id)->first();
      $fileName = $this->createFilename($file);
      // Group files by mime type
      $mime = str_replace('/', '-', $file->getMimeType());
      // Group files by the date (week
      $dateFolder = date("d-m-y");

      // Build the file path

      //$filePath1 = "video/{$dateFolder}/{$mime}/";
    //  $thumbPath = "video/{$dateFolder}/thumbnail/";

      //echo $filePath.$fileName;die;
      // $host_url =url('/')."/storage/video/{$dateFolder}/{$mime}/$fileName";
      $filePath_video = "public/video/seller/{$dateFolder}/video/{$mime}";
      $filePath = "video/seller/{$dateFolder}/video/{$mime}";
      $filePath_image = "public/video/seller/{$dateFolder}/image/{$mime}/";
      $thumbPath = "video/seller/{$dateFolder}/thumbnail/";
      $file->storeAs($filePath_video, $fileName);
      // $media_thumb=FFMpeg::fromDisk('public')
      //     ->open($filePath.$fileName)
      //     ->getFrameFromSeconds(5)
      //     ->export()
      //     ->toDisk('public')
      //     ->save($thumbPath."$fileName.png");
      $main_url=config('constant.url1');
      $download_url="{$main_url}/{$filePath_video}/{$fileName}";

      $user_id=$request->user_id;
      $video = new VideoList();
      $video->request_id = uniqid();
      $video->file_name = $fileName;
      $video->user_id = $user_id;
      $video->seller_id=$request->seller_id;
      $video->seller_name=$seller_info->display_name;
      $video->download_url = $download_url;
      $video->demo_url= $download_url;
      // $video->small_thumb =$small_host_url;
      // $video->medium_thumb = $medium_host_url;
      // $video->large_thumb= $large_host_url;
      $video->extension=$file->getClientOriginalExtension();
      $video->video_length=$file->getSize();
      $video->save();
      if($video)
      {
        return response(array(
          'success'=>1,
          'video_id'=>$video->id,
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

  /**
   * Create unique filename for uploaded file
   * @param UploadedFile $file
   * @return string
   */
  protected function createFilename(UploadedFile $file)
  {
      $extension = $file->getClientOriginalExtension();
      $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

      // Add timestamp hash to name of the file
      $filename .= "_" . md5(time()) . "." . $extension;

      return $filename;
  }


//---------Insert Upload Video Description------------
public function insertVideoDescription(Request $request)
{
  $validator=Validator::make($request->all(), [
     'user_id'=>'required',
     'video_id'=>'required',
     'seller_id'=>'required',
     'tag'=>'required',
     'keywords'=>'required',
     'mature_content'=>'required',
     'title'=>'required',
     'cast'=>'required',
     'director'=>'required',
     'producer'=>'required',
     'genre'=>'required',
     'language_id'=>'required',
     'artist_name'=>'required',
     'description'=>'required',
     'short_desc'=>'required',
     'territory_rights'=>'required',
     'license_rights'=>'required',
     'price'=>'required',
     'file.*.image'=>'required',
     'video_category_id'=>'required',
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
      if($request->hasFile('image'))
      {
          $status_check=VideoList::where(['id'=>$request->video_id,'user_id'=>$request->user_id])->count();
          if($status_check==1)
          {
            $file=$request->file('image');
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
            $host_url ="{$base_url}video/seller/{$dateFolder}/thumbnail/{$mime}/$fileName";
            $largeimage = Image_flav::make($file);
            $path = Storage::put("/public/video/seller/{$dateFolder}/thumbnail/{$mime}/$fileName", (string) $largeimage->encode());
            $seller_name=Seller::where('id',$request->seller_id)->first();
         $data=VideoList::where('id',$request->video_id)->update([
         'video_category_id'=>$request->video_category_id,
         'title'=>$request->title,
         'artist_name'=>$request->artist_name,
         'description'=>nl2br($request->description),
         'large_thumb'=>$host_url,
         'seller_id'=>$request->seller_id,
         'seller_name'=>$seller_name->display_name,
         'short_desc'=>nl2br($request->short_desc),
         'territory_rights'=>$request->territory_rights,
         'license_rights'=>$request->license_rights,
         'price'=>$request->price,
         'mature_content'=>$request->mature_content,
         'director'=>$request->director,
         'genre'=>$request->genre,
         'producer'=>$request->producer,
         'cast'=>$request->cast,
         'language_id'=>$request->language_id,
         'keywords'=>$request->keywords,
         'status'=>2
       ]);
       if($data)
       {
         return response(array(
           'success'=>1,
           'data'=>[],
           'msg'=>'Video Description Submitted Successfully'
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
            'msg'=>'Unauthorized'
          ),Response::HTTP_UNAUTHORIZED);
        }

    }
  }
  }
  //------------------END-------------------------------
  public function getSellerVideoList(Request $request)
  {
    $validator=Validator::make($request->all(), [
       'user_id'=>'required',
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
          $user_count=User::where(['id'=>$request->user_id,'user_role'=>'a'])->count();
          if($user_count)
          {
             if($request->video_id=='')
             {
               $data=VideoList::all();
             }
             else {
               $data=VideoList::where('id',$request->video_id)->first();
             }
          }
          else {
            $seller_status=Seller::where('id',$request->user_id)->count();
            if($seller_status)
            {
              if($request->video_id=='')
              {
                $data=VideoList::where('user_id',$request->user_id)->get();
              }
              else
              {
                $data=VideoList::where(['user_id'=>$request->user_id,'id'=>$request->video_id])->first();
              }
            }
            else {
              return response(array(
                'success'=>0,
                'data'=>[],
                'msg'=>'User is not registered as seller'
              ),Response::HTTP_UNAUTHORIZED);
            }
          }
          if($data)
          {
            return response(array(
              'success'=>1,
              'data'=>$data
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
}
