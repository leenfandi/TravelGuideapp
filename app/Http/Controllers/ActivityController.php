<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\City;
use App\Models\Image;
use App\Models\Rate;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
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
        $activity=Activity::create(array_merge(
            $validator->validated()
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



        return response()->json([
            'message'=>'Activity added successfully',

        ],201);


    }



public function GetNearbyByLocation (Request $request)
{
    $latitude = $request->latitude;
    $longitude = $request->longitude;
    $radius = 20000;
    $activities = Activity::where('latitude' , '>' , $latitude - $radius)
                        ->where('latitude' , '<' , $latitude + $radius)
                        ->where('longitude' , '>' , $longitude - $radius)
                        ->where('longitude' , '<' , $longitude + $radius)->get();

    foreach($activities as $activity )
    {
      $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
      $activity->image = Image::select('url')->where('activity_id' , $activity->id)->first();
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

    return response()->json([
     'data'=> $region,

    ],201);
}

public function GetAllCities()
{
    $cities = City::all();

    return response()->json([
        $cities

    ],200);
}

public function GetAllRegions()
{
    $regions = Region::all();

    return response()->json([
        $regions

    ],200);
}

public function GetRegionsInCity ($city_id)
{
    $regions = Region::where('city_id' , $city_id)->get();

    return response()->json([
        $regions
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
        'cities ' => $cities
    ],200);
}

public function getallactivities()
{
    $activities = Activity::select('id','region_id', 'name', 'type', 'description', 'price', 'latitude', 'longitude')
    ->paginate(10);


    $formedData = [];

    foreach ($activities as $activity) {

        $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
        $activity->region = Region::where('id'  ,$activity->region_id)->first();
        $activity->city = City::where('id' , $activity->region->city_id)->first();
        $activity->user = Admin::where('id , ');

    }

    return response()->json([
        'message' => 'Get activities with images successfully',
        'data' => $activities
    ], 201);
}

    }
