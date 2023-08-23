<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Bookmark;
use App\Models\City;
use App\Models\Comment;
use App\Models\Guide;
use App\Models\Image;
use App\Models\Rate;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BookmarkController extends Controller
{
    // for user
    public function AddBookmark(Request $request)
    {
        $user_id = Auth::guard('api')->id();
        $bookmark = Bookmark::where('user_id', $user_id)->where('activity_id', $request->activity_id)->first();
        if ($bookmark) {
            Bookmark::where('activity_id', $request->activity_id)->where('user_id', $user_id)->delete();
            return response()->json([
                'message' => 'bookmark deleted',
                'added' => false,
            ], 200);
        } else {
            Bookmark::create([
                'user_id' => $user_id,
                'activity_id' => $request->activity_id
            ]);
            return response()->json([
                'message' => 'bookmark added',
                'added' => true,
            ], 200);
        }
    }
    // for guide
    public function AddBookmarkForGuide(Request $request)
    {
        $guide_id = Auth::guard('guide-api')->id();
        Activity::where('id', $request->activity_id)->get();
        $bookmark = Bookmark::where('guide_id', $guide_id)->where('activity_id', $request->activity_id)->first();
        if ($bookmark) {
            Bookmark::where('activity_id', $request->activity_id)->where('guide_id', $guide_id)->delete();

            return response()->json([
                'message' => 'bookmark deleted',
                'added' => false,
            ], 200);
        } else {
            Bookmark::create([
                'guide_id' => $guide_id,
                'activity_id' => $request->activity_id
            ]);

            return response()->json([
                'message' => 'bookmark added',
                'added' => true,
            ], 200);
        }
    }
    // for user
    public function GetBookmarks()
    {
        $user_id = Auth::guard('api')->id();
        $bookmarks = Bookmark::where('user_id', $user_id)->latest()->get();
        $activities = [];
        foreach ($bookmarks as $bookmark) {
            $activity = Activity::where('id', $bookmark->activity_id)->first();
            if ($activity) {
                $activities[] = $activity;
            }
        }

        foreach ($activities as $activity) {
            $activity->rating = round(Rate::where('activity_id', $activity->id)->avg('rate'), 1);
            $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
            $activity->region = Region::where('id', $activity->region_id)->first();
            $activity->city = City::where('id', $activity->region->city_id)->first();
            $activity->comments = Comment::where('activities_id', '=', $activity->id)->count();
            $activity->bookmarked = true ;
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

        return response()->json([
            'message' => 'Bookmarked activities ',
            'data' => $activities,

        ], 200);
    }
    // for guide
    public function GetBookmarksForGuide()
    {

        $guide_id = Auth::guard('guide-api')->id();
        $bookmarks = Bookmark::where('guide_id', $guide_id)->latest()->get();
        $activities = [];
        foreach ($bookmarks as $bookmark) {
            $activity = Activity::where('id', $bookmark->activity_id)->first();
            if ($activity) {
                $activities[] = $activity;
            }
        }

        foreach ($activities as $activity) {
            $activity->rating = round(Rate::where('activity_id', $activity->id)->avg('rate'), 1);
            $activity->urls = Image::select('url')->where('activity_id', $activity->id)->orderBy('id', 'desc')->get();
            $activity->region = Region::where('id', $activity->region_id)->first();
            $activity->city = City::where('id', $activity->region->city_id)->first();
            $activity->comments = Comment::where('activities_id', '=', $activity->id)->count();
            $activity->bookmarked = true ;
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

        return response()->json([
            'message' => 'Bookmarked activities ',
            'data' => $activities,

        ], 200);
    }
}
