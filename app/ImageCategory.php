<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageCategory extends Model
{
	/*--------------amar------------------*/
    public function getImageCount()
    {
    	return $this->hasMany(ImageList::class, 'image_category_id')->select("*");
    }

    public function getImageCount_1()
    {
        return $this->hasMany(ImageList::class, 'image_category_id')->select('id','large_thumb', 'size','title','image_category_id','premium', 'small_thumb','medium_thumb',"description","artist_name" ,"total_buy" ,"status", "keywords",'dimension','extension','large_thumb','short_desc', 'user_id','price')->where('status',1);
    } 
}
