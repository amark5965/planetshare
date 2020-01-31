<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoCategory extends Model
{
	/*--------------amar------------------*/
     public function getVideoCount()
    {
    	return $this->hasMany(VideoList::class, 'video_category_id')->select("*");
    }

    public function getVideoCount_1()
    {
        return $this->hasMany(VideoList::class, 'video_category_id')->select('id','large_thumb', 'size','title','video_category_id','premium', 'price','small_thumb', 'cast','medium_thumb',"description","artist_name" ,"status", "keywords",'dimension','extension','large_thumb','short_desc', 'user_id','seller_id')->where('status',1);
    } 
}
