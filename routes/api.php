<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/getHomeData',[
// 	'uses'=>'Mobile\CommonController@getHomeData'
// ]);

Route::group(['prefix'=>'mobile'],function(){

	//route for home data services
	Route::get('/getHomeData',[
	'uses'=>'Mobile\CommonController@getHomeData'
    ]);

    //route for get data for specific
    Route::post('/viewAll',[
    	'uses'=>'Mobile\CommonController@viewAll'
    ]);

    //detail of gig
    Route::post('/giginfo',[
        'uses'=>'Mobile\CommonController@giginfo'
    ]);

    Route::post('/image_detail','Mobile\CommonController@image_detail');

    Route::post('/video_detail','Mobile\CommonController@video_detail');


    Route::get('/home_page',[
        'uses'=>'Mobile\CommonController@home_page'
    ]);

    Route::post('/view_all',[
        'uses'=>'Mobile\CommonController@view_all'
    ]); 

   

});

//route for social login
   Route::post('/login/{provider}',[
    'uses'=>'Auth\SocialAccountController@findOrCreateUser'
   ]);


   

