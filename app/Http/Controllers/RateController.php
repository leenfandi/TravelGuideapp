<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Guide;
use App\Models\Guide_Rates;
use App\Models\Image;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    public function SetRate ($activity_id , Request $request)
    {
        $user_id = Auth::guard('api')->id();
        $activity = Activity::where('id',$activity_id)->first();
        if($activity)
        {
            $validator = Validator($request->all() , [
                'rate' => 'required|int'
            ]);
            if($validator->fails())
            {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rate = Rate::create([
                'rate' => $request->rate ,
                'user_id' => $user_id,
                'activity_id' => $activity_id
            ]);
            return response()->json([
                'message'=>'Rate added',
            ],200);
        }
        return response()->json([
            'message' => 'activity not found',
        ], 422);

    }

    public function SetRateForGuide ($activity_id , Request $request)
    {
        $guide_id = Auth::guard('guide-api')->id();
        $activity = Activity::where('id',$activity_id)->first();
        if($activity)
        {
            $validator = Validator($request->all() , [
                'rate' => 'required|int'
            ]);
            if($validator->fails())
            {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rate = Rate::create([
                'rate' => $request->rate ,
                'guide_id' => $guide_id,
                'activity_id' => $activity_id
            ]);
            return response()->json([
                'message'=>'Rate added',
            ],200);

        }
        return response()->json([
            'message' => 'activity not found',
        ], 422);

    }

    public function GetTopRated()
    {
       $activities  = Activity::all();

       foreach($activities as $activity )
       {
            $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
            $activity->image = Image::select('url')->where('activity_id' , $activity->id)->first();
       }
          $topRated = $activities->sortByDesc('rating')->take(10);

         return response()->json([
           'Top_Rated'=> $topRated
         ]  ,200);
    }

    // to let user rate a guide
    public function PutRateToGuide ($guide_id , Request $request)
    {
        $user_id = Auth::guard('api')->id();
        $guide = Guide::where('id',$guide_id)->first();
        if($guide)
        {
            $validator = Validator($request->all() , [
                'rate' => 'required|int'
            ]);
            if($validator->fails())
            {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rate = Guide_Rates::create([
                'rate' => $request->rate ,
                'user_id' => $user_id,
                'guide_id' => $guide_id
            ]);
            return response()->json([
                'message'=>'Rate added',
            ],200);
        }
        return response()->json([
            'message' => 'guide not found',
        ], 422);

    }

    public function GetTopGuides()
    {
       $guides  = Guide::all();

       foreach($guides as $guide )
       {
            $guide->rating = round(Guide_Rates::where('guide_id' , $guide->id)->avg('rate'),1);
            $guide->image = Guide::select('image')->where('id' , $guide->id)->first();
       }
          $topGuides = $guides->sortByDesc('rating')->take(10);

         return response()->json([
           'Top_Guides'=> $topGuides
         ]  ,200);
    }
}
