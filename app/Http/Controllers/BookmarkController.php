<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Bookmark;
use App\Models\Image;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BookmarkController extends Controller
{
    // for user
    public function AddBookmark($activity_id)
    {
       $user_id = Auth::guard('api')->id();
       $activity = Activity::where('id' , $activity_id)->get();
       $bookmark = Bookmark::where('user_id' , $user_id)->where('activity_id' , $activity_id)->first();
        if($bookmark){
           $query = Bookmark::where('activity_id' , $activity_id)->where('user_id' , $user_id)->delete();

           return response()->json([
            'message'=>'bookmark deleted',
        ],200);
        }
        else {
            $newBookmark = Bookmark::create([
                'user_id' => $user_id ,
                'activity_id' => $activity_id
            ]);

            return response()->json([
                'message'=>'bookmark added',
            ],200);
        }

    }
    // for guide
    public function AddBookmarkForGuide($activity_id)
    {
       $guide_id = Auth::guard('guide-api')->id();
       $activity = Activity::where('id' , $activity_id)->get();
       $bookmark = Bookmark::where('guide_id' , $guide_id)->where('activity_id' , $activity_id)->first();
        if($bookmark){
           $query = Bookmark::where('activity_id' , $activity_id)->where('guide_id' , $guide_id)->delete();

           return response()->json([
            'message'=>'bookmark deleted',
        ],200);
        }
        else {
            $newBookmark = Bookmark::create([
                'guide_id' => $guide_id ,
                'activity_id' => $activity_id
            ]);

            return response()->json([
                'message'=>'bookmark added',
            ],200);
        }

    }
    // for user
    public function GetBookmarks()
    {
        $user_id = Auth::guard('api')->id();
        $bookmarks = Bookmark::where('user_id' , $user_id)->latest()->get();
        $activities = [];
        foreach($bookmarks as $bookmark)
        {
            $activity = Activity::where('id' , $bookmark->activity_id)->first();
            if($activity){
            $activities[] = $activity;}
        }

        foreach($activities as $activity )
        {
             $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
             $activity->image = Image::select('url')->where('activity_id' , $activity->id)->first();
        }

        return response()->json([
            'message'=>'Bookmarked activities ',
            'data'=>$activities,

        ],200);
    }
        // for guide
    public function GetBookmarksForGuide()
    {

        $guide_id = Auth::guard('guide-api')->id();
        $bookmarks = Bookmark::where('guide_id' , $guide_id)->latest()->get();
        $activities = [];
        foreach($bookmarks as $bookmark)
        {
            $activity = Activity::where('id' , $bookmark->activity_id)->first();
            if($activity){
            $activities[] = $activity;}
        }

        foreach($activities as $activity )
        {
             $activity->rating = round(Rate::where('activity_id' , $activity->id)->avg('rate'),1);
             $activity->image = Image::select('url')->where('activity_id' , $activity->id)->first();
        }

        return response()->json([
            'message'=>'Bookmarked activities ',
            'data'=>$activities,

        ],200);
    }
}
