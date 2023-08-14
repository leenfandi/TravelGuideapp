<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\User;
use App\Models\Activity;
use App\Models\City;
use App\Models\Guide;
use App\Models\Image;
use App\Models\Rate;
use App\Models\Region;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{

    public function AddImages(Request $request)
    {
        $paths = [];
        foreach($request->images as $image){

             $file_extension = $image->extension();
                $file_name = time() . rand(1,100) .'.' . $file_extension;
                $image->move(public_path('images/activity_images'), $file_name);
                $path = "public/images/activity_images/$file_name";
                $paths[] = $path;

            }

       return response()->json([
                'message'=>'Images added successfully',
                'data' =>  $paths ,

                  ],201);
    }

    /*
    public function Addimage(Request $request)
    {
        $input = $request->all();
       $image = new Image();

       $image->activity_id = $input['activity_id'];

        if ($request->url && $request->url->isValid()){

            //  $photo=$request->url;
              $file_extension = $request->url->extension();
                $file_name = time() . '.' . $file_extension;
                $request->url->move(public_path('images/activity_images'), $file_name);
                $path = "public/images/activity_images/$file_name";
                $image->url = $path;



          $image->save();

          return response()->json([
            'message'=>'image added succefully',
            'image'=>$image,

        ]);
    }

       /* if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }*/


       // return response()->json([
         //   'message'=>'Activity added successfully',

     //   ],201);


  //  }
    public function get_Activity_With_Image($activity_id){


        $activity = Activity::where('id' , $activity_id)->first();
        if($activity)
        {
            $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
            $activity->urls=Image::select('url')->where('activity_id',$activity_id)
            ->orderBy('id','desc')->get();
            $activity->region = Region::where('id'  ,$activity->region_id)->first();
            $activity->city = City::where('id' , $activity->region->city_id)->first();
            if($activity->admin_id != null){
                $activity->admin = Admin::where('id' , $activity->admin_id)->first();
            }
            if($activity->guide_id != null){
                $activity->guide = Guide::where('id' , $activity->guide_id)->first();
            }
        }
           return response()->json([
            'message'=>' get Activity with image successfully',
              'data' => $activity,

        ],201);
    }





    }




