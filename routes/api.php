<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\GuideAuthController;
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

});


Route::group([
    'middleware' => 'App\Http\Middleware\Guide:guide-api',
    'prefix' => 'for_guide',

], function () {



});



Route::get('profile',function(){
    return 'unautheantic user ';
})->name('login');

Route::middleware('auth:api')->group(function ()
    {

       Route::get('getweather/{city}',[WeatherController::class, 'getWeatherData']);

    });
