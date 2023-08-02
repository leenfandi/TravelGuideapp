<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Image;
use GuzzleHttp\Promise\Create;
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
           // 'distance' => 'required' ,
            //'time' => 'required'
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

    public function AddActivityWithImages (Request $request)
    {
        $validator = Validator::make($request->all() ,[
            'region_id'=>'required',
            'name'=>'required',
            'type'=>'required',
            'description'=>'required',
            'price' => 'required' ,
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $activity=Activity::create(array_merge(
            $validator->validated()
        ));

        foreach ($request->images as $imagefile) {

           $image = new Image();
            $image->activity_id = $activity->id;
            if ($imagefile->isValid()){
            $photoname=time().'.jpg';
            $imagefile->store('/' , 'activity');
            $path="public/activity/$photoname";
            $image->url = $path;
            $image->save();

        }
    }
        return response()->json([
            'message'=>'Activity added successfully',
            'ac_id' => $activity->id
        ],201);

    }

}

