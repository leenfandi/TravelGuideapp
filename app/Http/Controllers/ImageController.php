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

class ImageController extends Controller
{
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

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }


        return response()->json([
            'message'=>'Activity added successfully',

        ],201);


    }
    public function add_Activity_With_Image(Request $request){

        $input = $request->all();
        $images = new Image();

        $images->activity_id = $input['activity_id'];

         $activity = Activity::select('region_id','name','type','description','price')->where('id' , $images->activity_id)
         ->latest()->first();
        // $url = Image::select('url','activity_id')->where('activity_id' , $images->activity_id)->latest()->first();

            $formedData['your activity'][] =
            [

                'url'=> asset('images/activity_images/' . $this->images->url),
                'activity_id' => $images->activity->id,
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




