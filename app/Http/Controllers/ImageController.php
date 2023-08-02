<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\User;
use App\Models\Activity;
use App\Models\Guide;
use App\Models\Image;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function Addimage(Request $request)
    {
        $input = $request->all();
       $image = new Image();

       $image->activity_id = $input['activity_id'];

        if ($request->url && $request->url->isValid()){

              $photo=$request->url;

          $photoname=time().'.jpg';
          Storage::disk('images')->put($photoname,base64_decode($photo));
          $path="public/images/activity_images/$photoname";
          $image->url = $path;
          }

          $image->save();

          return response()->json([
            'message'=>'image added succefully',
            'image'=>$image,

        ]);


        /*if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }*/


        return response()->json([
            'message'=>'Activity added successfully',

        ],201);


    }
    public function add_Activity_With_Image(Request $request){

        $input = $request->all();
        $images = new Image();

        $images->activity_id = $input['activity_id'];

         $activity = Activity::select('region_id','name','type','description','price')->where('id' , $images->activity_id)->latest()->first();
         $url = Image::select('url','activity_id')->where('activity_id' , $images->activity_id)->latest()->first();

            $formedData['your activity'][] =
            [

                'url'=> $url->url,
                'activity_id' => $url->activity->id,
               'region_id' => $activity->region->id ,
               'name' => $activity->name ,
               'type' => $activity ->type ,
               'description' => $activity ->description,
               'price' => $activity -> price,


            ];

           return response()->json([
            'message'=>' get Activity with image successfully',
            'data' => $formedData,

        ],201);
    }

    }




