<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyerOttOrders extends Model
{
  public function imageItemInfo()
  {
    return $this->belongsTo(ImageList::class,'item_id')->select('id','large_thumb','short_desc','request_id','price','title')->orderBy('created_at','desc');
  }
  public function videoItemInfo()
  {
    return $this->belongsTo(VideoList::class,'item_id')->select('id','large_thumb','short_desc','request_id','price','title')->orderBy('created_at','desc');
  }
}
