<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\City;
use App\Models\Guide;
use App\Models\Image;
use App\Models\Rate;
use App\Models\Region;
use App\Models\Region_Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    protected $db_mysql;
    public function __construct()
    {
        $this ->db_mysql= config('database.connections.mysql.database');

    }


    public function AddActivity(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'region_id'=>'required',
            'name'=>'required',
            'type'=>'required',
            'description'=>'required',
            'price' => 'required' ,
            'latitude' => 'required' ,
            'longitude' => 'required' ,


        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        if(Auth::guard('admin-api')->user()){
        $activity=Activity::create(array_merge(
            $validator->validated() ,
             [ 'admin_id' => Auth::guard('admin-api')->id()]
        ));
        if($request->has('images')){
        $images = [];
        foreach($request->images as $image)
        {
            $images[] = Image::create([
                'activity_id' => $activity->id ,
                'url' => $image
            ]);
        }}
    }
         if(Auth::guard('guide-api')->user()){
            $activity=Activity::create(array_merge(
            $validator->validated()  ,
              ['guide_id' => Auth::guard('guide-api')->id()]
             ));
             if($request->has('images')){
                 $images = [];
                foreach($request->images as $image)
                 {
            $images[] = Image::create([
                'activity_id' => $activity->id ,
                'url' => $image
            ]);
             }}
         }



        return response()->json([
            'message'=>'Activity added successfully',

        ],201);


    }



public function GetNearbyByLocation (Request $request)
{
    $latitude = $request->latitude;
    $longitude = $request->longitude;
    $radius = 40;
    $activities = Activity::where('latitude' , '>' , $latitude - $radius)
                        ->where('latitude' , '<' , $latitude + $radius)
                        ->where('longitude' , '>' , $longitude - $radius)
                        ->where('longitude' , '<' , $longitude + $radius)->get();

    foreach($activities as $activity )
    {
        $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
        $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
        $activity->region = Region::where('id'  ,$activity->region_id)->first();
        $activity->city = City::where('id' , $activity->region->city_id)->first();
        if($activity->admin_id != null){
            $activity->user = Admin::select('id' , 'name' )->where('id' , $activity->admin_id)->first();
            $activity->user->image = null;
            $activity->user->type = 'admin';
        }
        if($activity->guide_id != null){
            $activity->user = Guide::select('id' , 'name' , 'image')->where('id' , $activity->guide_id)->first();
            $activity->user->type = 'guide';
        }
    }

     return response()->json([
        'message'=>'Nearby activities',
        'data'=>$activities,

    ],201);
}

public function addCity (Request $request)
{
    $city = City::create([
        'name' => $request->name
    ]);

    return response()->json([
        'data'=>$city,

    ],201);
}

public function addRegion (Request $request)
{
    $region = Region::create([
        'city_id' => $request->city_id ,
        'name' => $request->name
    ]);
    if($request->has('images')){
        $images = [];
        foreach($request->images as $image)
        {
            $images[] = Region_Image::create([
                'region_id' => $region->id ,
                'url' => $image
            ]);
        }}

    return response()->json([
     'data'=> $region,

    ],201);
}

public function GetAllCities()
{
    $cities = City::all();

    return response()->json([
        'message'=>'All Cities',
        'data' =>  $cities

    ],200);
}

public function GetAllRegions()
{
    $regions = Region::with("city")->get();

    foreach($regions as $region){
        $region->images = Region_Image::select('url')->where('region_id' , $region->id)->get();
        }

    return response()->json([
        'message'=>'All regions',
        'data' => $regions

    ],200);
}

public function GetRegionsInCity (Request $request)
{
    $regions = Region::where('city_id' , $request->city_id)->get();

    foreach($regions as $region){
    $region->images = Region_Image::select('url')->where('region_id' , $region->id)->get();
    }

    return response()->json([
        'message'=>'Regions in the city',
        'data' => $regions
    ],200);
}

public function GetEverything()
{
    $cities = City::all();

    foreach($cities as $city)
    {
        $city->regions = Region::where('city_id' , $city->id)->get();
    }

    return response()->json([
        'message'=>'All regions in all cities',
        'data ' => $cities
    ],200);
}

public function getallactivities()
{
    $activities = Activity::select('id','region_id', 'name', 'type', 'description', 'price', 'latitude', 'longitude' , 'admin_id' , 'guide_id')
    ->paginate(10);
/////k

    $formedData = [];

    foreach ($activities as $activity) {
        $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
        $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
        $activity->region = Region::where('id'  ,$activity->region_id)->first();
        $activity->city = City::where('id' , $activity->region->city_id)->first();
        if($activity->admin_id != null){
            $activity->user = Admin::select('id' , 'name' )->where('id' , $activity->admin_id)->first();
            $activity->user->image = null;
            $activity->user->type = 'admin';
        }
        if($activity->guide_id != null){
            $activity->user = Guide::select('id' , 'name' , 'image')->where('id' , $activity->guide_id)->first();
            $activity->user->type = 'guide';
        }

    }

    return response()->json([
        'message' => 'Get activities with images successfully',
        'data' => $activities
    ], 200);
}

    public function GetGuideActivities (Request $request)
    {
        $activities = Activity::where('guide_id' , $request->guide_id)->paginate(5);
        foreach ($activities as $activity) {
            $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
            $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
            $activity->region = Region::where('id'  ,$activity->region_id)->first();
            $activity->city = City::where('id' , $activity->region->city_id)->first();
        }

        return response()->json([
            'message' => 'Get activities successfully' ,
            'data' => $activities
        ]);


    }

    }
