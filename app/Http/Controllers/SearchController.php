<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Admin;
use App\Models\Bookmark;
use App\Models\City;
use App\Models\Comment;
use App\Models\User;
use App\Models\Region;
use App\Models\Guide;
use App\Models\Image;
use App\Models\Rate;
use Dotenv\Validator;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;


class SearchController extends Controller
{
    //for user
    public function autocompletesearch(Request $request)
    {
        $activities = Activity::where("name", "LIKE", "%{$request->name}%")->get();

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
            if (Auth::guard('guide-api')->user()) {
                $user =  (Auth::guard('guide-api')->user());
                $bookmark = Bookmark::where('guide_id', $user->id)->where('activity_id', $activity->id)->first();
                if ($bookmark) {
                    $activity->bookmarked = true;
                } else {
                    $activity->bookmarked = false;
                }
            } else  if (Auth::guard('api')->user()) {
                $user =  (Auth::guard('api')->user());
                $bookmark = Bookmark::where('user_id', $user->id)->where('activity_id', $activity->id)->first();
                
                if ($bookmark) {
                    $activity->bookmarked = true;
                } else {
                    $activity->bookmarked = false;
                }
            }
        }

        return response()->json([
            'message' => 'search result',
            'data' => $activities,

        ], 201);
    }
    //for user
    public function get_search_history()
    {

        $user_id = Auth::guard('api')->user()->id;

        $searchHistory = SearchHistory::select('id', 'text_search', /*'region_id',*/ 'user_id')
            ->where('user_id', $user_id)
            ->get();

        if (!$searchHistory) {
            return response()->json(['message' => 'Search history not found'], 404);
        }

        return response()->json([
            'data' => $searchHistory,

        ]);
    }
    // for guide
    public function get_search_history_guide()
    {

        $guide_id = Auth::guard('guide-api')->user()->id;

        $searchHistory = SearchHistory::select('id', 'text_search', /*'region_id',*/ 'guide_id')
            ->where('guide_id', $guide_id)
            ->get();

        if (!$searchHistory) {
            return response()->json(['message' => 'Search history not found'], 404);
        }

        return response()->json([
            'data' => $searchHistory,

        ]);
    }
}
