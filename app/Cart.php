<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function imageItemInfo()
    {
      return $this->belongsTo(ImageList::class,'item_id')->select('id','large_thumb','short_desc','request_id','price','title');
    }
    public function videoItemInfo()
    {
      return $this->belongsTo(VideoList::class,'item_id')->select('id','large_thumb','short_desc','request_id','price','title');
    }
    public function getItemData()
    {
      dd($this);
      die;
    //  return $this->belongsTo(ImageList::class,'item_id')->select('id','large_thumb','short_desc','request_id','price','title');
    }
}
