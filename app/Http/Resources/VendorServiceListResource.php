<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorServiceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      //  return parent::toArray($request);
      return [
        'id'=>$this->id,
        'service_name'=>$this->service_name,
        'service_id'=>$this->service_id,
        'children'=>$this->children
      ];
    }
}
