<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Image;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
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
            'price' => 'required'

        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $activity=Activity::create(array_merge(
            $validator->validated()
        ));


        return response()->json([
            'message'=>'Activity added successfully',

        ],201);


    }
    public function nearby_activity_by_type( Request $request ){


        $validator =Validator::make($request->all(),[

            'type ' => 'required|string',

        ]);

        if ($validator->fails()) {
           return response()->json(['error'=>$validator->errors()]);
       }
      $activity = Activity::select('name','type','description')->where('type',$request->type)->get();

      return response()->json([
        'message'=>$activity,

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
        'data'=>$activities,

    ],201);
}

}
