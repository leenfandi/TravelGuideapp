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
use App\Models\Admin;
use App\Models\City;
use App\Models\Comment;
use App\Models\Region;
use App\Models\Region_Image;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RateController extends Controller
{
    public function SetRate(Request $request)
    {
        $user_id = Auth::guard('api')->id();
        $activity = Activity::where('id', $request->activity_id)->first();
        if ($activity) {
            $validator = Validator($request->all(), [
                'activity_id' => 'required',
                'rate' => 'required|int'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rate = Rate::create([
                'rate' => $request->rate,
                'user_id' => $user_id,
                'activity_id' => $request->activity_id
            ]);
            return response()->json([
                'message' => 'Rate added',
            ], 200);
        }
        return response()->json([
            'message' => 'activity not found',
        ], 422);
    }
    // let guide rate an activity
    public function SetRateForGuide(Request $request)
    {
        $guide_id = Auth::guard('guide-api')->id();
        $activity = Activity::where('id', $request->activity_id)->first();
        if ($activity) {
            $validator = Validator($request->all(), [
                'rate' => 'required|int'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rate = Rate::create([
                'rate' => $request->rate,
                'guide_id' => $guide_id,
                'activity_id' => $request->activity_id
            ]);
            return response()->json([
                'message' => 'Rate added',
            ], 200);
        }
        return response()->json([
            'message' => 'activity not found',
        ], 422);
    }

    public function GetTopRated()
    {
        $activities = Activity::all();

        foreach ($activities as $activity) {
            $activity->rating = round(Rate::where('activity_id', $activity->id)->avg('rate'), 1);
            $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
            $activity->region = Region::where('id', $activity->region_id)->first();
            $activity->city = City::where('id', $activity->region->city_id)->first();
            $activity->comments = Comment::where('activities_id', '=', $activity->id)->count();

            if ($activity->admin_id != null) {
                $activity->user = Admin::select('id', 'name')->where('id', $activity->admin_id)->first();
                $activity->user->image = null;
                $activity->user->type = 'admin';
            }
            if ($activity->guide_id != null) {
                $activity->user = Guide::select('id', 'name', 'image')->where('id', $activity->guide_id)->first();
                $activity->user->type = 'guide';
            }
        }

        // Use the sortBy function to sort by the 'rating' attribute
        $topRated = $activities->sortByDesc('rating')->take(10);

        return response()->json([
            'message' => 'Top Rated activities',
            'data' => $topRated->values() // Reset indexes for the sorted collection
        ], 200);
    }

    // to let user rate a guide
    public function PutRateToGuide(Request $request)
    {
        $user_id = Auth::guard('api')->id();
        $guide = Guide::where('id', $request->guide_id)->first();
        if ($guide) {
            $validator = Validator($request->all(), [
                'rate' => 'required|int'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rate = Guide_Rates::create([
                'rate' => $request->rate,
                'user_id' => $user_id,
                'guide_id' => $request->guide_id
            ]);
            return response()->json([
                'message' => 'Rate added',
            ], 200);
        }
        return response()->json([
            'message' => 'guide not found',
        ], 422);
    }

    public function GetTopGuides()
    {
        $guides  = Guide::all();

        foreach ($guides as $guide) {
            $guide->rating = round(Guide_Rates::where('guide_id', $guide->id)->avg('rate'), 1);
            $guide->image = Guide::select('image')->where('id', $guide->id)->first();
        }
        $topGuides = $guides->sortByDesc('rating')->take(10);

        return response()->json([
            'Top_Guides' => $topGuides
        ], 200);
    }
}
