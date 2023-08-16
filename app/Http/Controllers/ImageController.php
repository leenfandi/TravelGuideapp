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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{

    public function AddImages(Request $request)
    {
        $paths = [];
        if($request->type == 'activity'){
        foreach($request->images as $image){

             $file_extension = $image->extension();
                $file_name = time() . rand(1,100) .'.' . $file_extension;
                $image->move(public_path('images/activity_images'), $file_name);
                $path = "public/images/activity_images/$file_name";
                $paths[] = $path;

            }
        }
        if($request->type == 'region'){
            foreach($request->images as $image){

                 $file_extension = $image->extension();
                    $file_name = time() . rand(1,100) .'.' . $file_extension;
                    $image->move(public_path('images/region_images'), $file_name);
                    $path = "public/images/region_images/$file_name";
                    $paths[] = $path;

                }
            }


       return response()->json([
                'message'=>'Images added successfully',
                'data' =>  $paths ,

                  ],201);
    }


    public function get_Activity_With_Image(Request $request){


        $activity = Activity::where('id' , $request->activity_id)->first();
        if($activity)
        {
            $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
            $activity->urls=Image::select('url')->where('activity_id',$request->activity_id)
            ->orderBy('id','desc')->get();
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
            'message'=>' get Activity with image successfully',
              'data' => $activity,

        ],200);
    }

    public function getImage(Request $request)
{
    $path = $request->get('path');

    $image = File::get($path);

    $base64Image = base64_encode($image);

    /*return response()->json([
        'image' => 'data:image/png;base64,' . $base64Image,

    ]);*/

    return Response::make($image , 200);

}
    }




