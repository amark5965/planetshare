<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
          'id'=>$this->id,
          'cat_name'=>$this->name,
          'cat_icon'=>$this->icon,
          'description'=>$this->desc,
          'small_img_url'=>$this->small_img,
          'medium_img_url'=>$this->medium_img,
          'large_img_url'=>$this->large_img,
        ];
    }
}
