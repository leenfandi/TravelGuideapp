<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\GuideAuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\RateController;

use App\Http\Controllers\SearchController;
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
Route::get('public/images/{folderName}/{filename}',[ImageController::class,'getImage']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([

    'middleware' => 'api',

], function ($router) {
    Route::group([ 'prefix' => 'user', ], function ($router) {

    Route::post('register',[RegisterController::class,'register']);
    Route::post('login',[RegisterController::class,'login']);
    Route::post('logout',[RegisterController::class,'logout']);
     });

     Route::group([ 'prefix' => 'admin', ], function ($router) {

        Route::post('register',[AdminAuthController::class,'register']);
        Route::post('login',[AdminAuthController::class,'login']);
        Route::post('logout',[AdminAuthController::class,'logout']);

     });
     Route::group([ 'prefix' => 'guide', ], function ($router) {


        Route::post('login',[GuideAuthController::class,'login']);
        Route::post('logout',[GuideAuthController::class,'logout']);

     });

});

Route::group([
    'middleware' => 'App\Http\Middleware\Admin:admin-api',
    'prefix' => 'for_admin',

], function () {


    Route::post('addguide',[AdminAuthController::class,'addguide']);
    Route::post('addimages',[ImageController::class,'AddImages']);
    Route::post('addactivity',[ActivityController::class,'AddActivity']);
    Route::get('showcomment',[CommentController::class, 'showcomment']);
    Route::delete('deletecomment/{activity_id}',[CommentController::class, 'deletecomment']);
    Route::post('getactivity',[ImageController::class, 'get_Activity_With_Image']);
    Route::get('get_all_guides',[AdminAuthController::class,'getProfile_of_guides']);
    Route::get('get_all_users',[AdminAuthController::class,'getProfile_of_users']);
    Route::delete('delete_any_guide/{id}',[AdminAuthController::class,'delete_any_guide']);
    Route::delete('delete_any_user/{id}',[AdminAuthController::class,'delete_any_user']);
    Route::get('getactivity',[ActivityController::class,'getallactivities']);
    Route::post('addcity',[ActivityController::class, 'addCity']);
    Route::post('addregion',[ActivityController::class, 'addRegion']);
    Route::get('getcities',[ActivityController::class,'GetAllCities']);
    Route::get('getregions',[ActivityController::class,'GetAllRegions']);
    Route::post('getregionsincity',[ActivityController::class,'GetRegionsInCity']);
    Route::get('getallregionsinallcities',[ActivityController::class,'GetEverything']);
    Route::post('getguide',[AdminAuthController::class,'getguide']);
    Route::post('getuser',[AdminAuthController::class,'getuser']);
    Route::post('getguideactivities',[ActivityController::class,'GetGuideActivities']);

});


Route::group([
    'middleware' => 'App\Http\Middleware\Guide:guide-api',
    'prefix' => 'for_guide',

], function () {

    Route::post('addcomment/{activity_id}',[CommentController::class, 'storecomment']);
    Route::get('showcomment/{activity_id}',[CommentController::class, 'showcomment']);
    Route::post('addimages',[ImageController::class,'AddImages']);
    Route::post('addactivity',[ActivityController::class,'AddActivity']);
    Route::post('addrate',[RateController::class, 'SetRateForGuide']);
    Route::post('addbookamrk/{activity_id}',[BookmarkController::class, 'AddBookmarkForGuide']);
    Route::get('bookmarked',[BookmarkController::class, 'GetBookmarksForGuide']);
    Route::post('search/{any_string_of_region}',[SearchController::class,'autocomplete_search']);
    Route::get('get_search_history',[SearchController::class, 'get_search_history_guide']);
    Route::get('nearbylocation',[ActivityController::class, 'GetNearbyByLocation']);
    Route::get('toprated',[RateController::class, 'GetTopRated']);
    Route::post('addcity',[ActivityController::class, 'addCity']);
    Route::post('addregion',[ActivityController::class, 'addRegion']);
    Route::get('getactivity',[ActivityController::class,'getallactivities']);
    Route::get('getcities',[ActivityController::class,'GetAllCities']);
    Route::get('getregions',[ActivityController::class,'GetAllRegions']);
    Route::get('getregionsincity',[ActivityController::class,'GetRegionsInCity']);
    Route::get('getallregionsinallcities',[ActivityController::class,'GetEverything']);
    Route::post('getactivity',[ImageController::class, 'get_Activity_With_Image']);
    Route::post('getuser',[AdminAuthController::class,'getuser']);

});



Route::get('profile',function(){
    return 'unautheantic user ';
})->name('login');

Route::middleware('auth:api')->group(function ()
    {
       Route::post('updateprofile',[RegisterController::class,'updateProfile']);
       Route::get('getweather/{city}',[WeatherController::class, 'getWeatherData']);
       Route::post('comment/{activity_id}',[CommentController::class, 'store']);
       Route::get('showcomment/{activity_id}',[CommentController::class, 'showcomment']);
       Route::get('activity/{activity_id}/comments',[CommentController::class, 'list']);
       Route::post('addrate',[RateController::class, 'SetRate']);
       Route::get('toprated',[RateController::class, 'GetTopRated']);
       Route::get('bookmarked',[BookmarkController::class, 'GetBookmarks']);
       Route::post('addbookamrk',[BookmarkController::class, 'AddBookmark']);
       Route::get('nearbylocation',[ActivityController::class, 'GetNearbyByLocation']);
       Route::delete('deletecomment',[CommentController::class, 'deletecommentuser']);
       Route::post('search/{any_string_of_region}',[SearchController::class,'autocompletesearch']);
       Route::get('get_search_history',[SearchController::class, 'get_search_history']);
       Route::post('rateaguide',[RateController::class, 'PutRateToGuide']);
       Route::get('topguides',[RateController::class, 'GetTopGuides']);
       Route::get('getactivity',[ActivityController::class,'getallactivities']);
       Route::get('getcities',[ActivityController::class,'GetAllCities']);
       Route::get('getregions',[ActivityController::class,'GetAllRegions']);
       Route::get('getregionsincity',[ActivityController::class,'GetRegionsInCity']);
       Route::get('getallregionsinallcities',[ActivityController::class,'GetEverything']);
       Route::post('getactivity',[ImageController::class, 'get_Activity_With_Image']);
       Route::post('getguide',[AdminAuthController::class,'getguide']);
        Route::post('getguideactivities',[ActivityController::class,'GetGuideActivities']);

       Route::post('change_password',[RegisterController::class,'changePassword']);
       Route::delete('deletemyaccount',[RegisterController::class,'DeleteMyAccount']);

    });
