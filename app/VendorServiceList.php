<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\VendorServiceListResource;
class VendorServiceList extends Model
{
	//   public function parent() {
	//   return $this->belongsToOne(static::class, 'parent_id');
	// }

	//each category might have multiple children
	public function children() {
	  return $this->hasMany(static::class, 'parent_id')->orderBy('service_name', 'asc');
	}
	public function getGigsList() {
	  return $this->hasMany(VendorServiceGig::class, 'category_id')->select("id","vendor_service_gigs_id","vendor_name","gig_title","vendor_id","language","basic_pack_price","gig_image_1","gig_image_2","gig_image_3","category_id")->where('status',1)->orderBy('created_at','desc');
	}
	public function vendorname()
	{
	    return $this->belongsTo(Vendor::class, 'id');
	}

    /*--------------amar------------------*/
	public function vendorServiceCount()
	{
	    return $this->hasMany(VendorServiceGig::class, 'category_id')->select("*");
	}
}
