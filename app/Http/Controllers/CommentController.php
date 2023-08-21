<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use App\Models\User;
use App\Models\Activity;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    //for user
    public function list(Request $request)
    {
        $activity = Activity::where('id', $request->activity_id)->first();
        if ($activity) {
            $comments=Comment::where('activities_id',$request->activity_id)
            ->get();
            foreach($comments as $comment)
            {
                if($comment->user_id != null)
                {
                    $comment->user= User::select('id' ,'name' , 'image')->where('id' , $comment->user_id)->first();
                    $comment->user->type = "user" ;
                }
                if($comment->guide_id != null)
                {
                    $comment->user= Guide::select('id' , 'name' , 'image')->where('id' , $comment->guide_id)->first();
                    $comment->user->type = "guide" ;
                }
            }


                return response()->json([
                    'message'=>'opinion of other ',
                    'data'=>$comments,

                ],200);



        }
        else{
            return response()->json([
                'message'=>'comment not found',

            ]);
        }

    }
    //for user
    public function store(Request $request)
    {   
        if(Auth::guard('api')->user() != null){
            $user = Auth::guard('api')->user();
            $user_id = $user->id;
            $activity = Activity::where('id', $request->activity_id)->get()->first();
            if ($activity) {
                $validator = validator($request->all(), [
                    'message' => 'required',
                ]);
            }
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
            $comment = Comment::create([
                'message' => $request->message,
                'activities_id' => $request->activity_id,
                'user_id' => $user->id,
            ]);
            $comment->user = [
                "image" => $user->image,
                "name" => $user->name,
                "id" => $user->id,
                "type" => "user"
            ];
    
            return response()->json([
                'message'=>'comment added',
                'data'=>$comment ,
    
            ],200);
        }
        else if(Auth::guard('guide-api')->user() != null){
            $user = Auth::guard('guide-api')->user() ;
            $user_id = $user->id;
            $activity = Activity::where('id', $request->activity_id)->get()->first();
            if ($activity) {
                $validator = validator($request->all(), [
                    'message' => 'required',
                ]);
            }
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
            $comment = Comment::create([
                'message' => $request->message,
                'activities_id' => $request->activity_id, 
                'guide_id' => $user->id,
            ]);
            $comment->user = [
                "image" => $user->image,
                "name" => $user->name,
                "id" => $user->id,
                "type" => "user"
            ];
    
            return response()->json([
                'message'=>'comment added',
                'data'=>$comment ,
    
            ],200);
        }
        return response()->json([
            'message' => 'error while adding comment'
        ], 400);
    }
    //for guide
    public function storecomment($activity_id, Request $request)
    {
        $guide_id= Auth::guard('guide-api')->user()->id;
        $activity = Activity::where('id', $activity_id)->get()->first();
        if ($activity) {
            $validator = validator($request->all(), [

                'message' => 'required',



            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
        $comment = Comment::create([
            'message' => $request->message,
            'activities_id' => $activity_id,
            'guide_id' => Auth::guard('guide-api')->id(),

        ]);


        return response()->json([
            'message'=>'comment added',
            //'name'=>$user->name,
            'data'=>$comment ,

        ],200);


    }

    //for user
    public function deletecommentuser( ){
        $user_id= Auth::guard('api')->user()->id;
        $comment = Comment::find($user_id);
        $result = $comment->delete();
        if($result){
            return response()->json([
                'message'=>' A Comment Deleted Successfully'
            ],201);
         } else{
            return response()->json([
                'message'=>'Comment Not Deleted '
            ],400);
            }
    }

  //  for admin
  public function deletecomment( $id){
    $comment = Comment::find($id);
    $result = $comment->delete();
    if($result){
        return response()->json([
            'message'=>' A Comment Deleted Successfully'
        ],201);
     } else{
        return response()->json([
            'message'=>'Comment Not Deleted '
        ],400);
        }
}

public function showcomment(Request $request)
    {
        $activity = Activity::where('id', $request->activity_id)->first();

        if($activity)
        {
            $comments = Comment::where('activities_id' , $request->activity_id)->get();

            foreach($comments as $comment)
            {
                if($comment->user_id != null)
                {
                    $comment->user= User::select('name' , 'image')->where('id' , $comment->user_id)->get();
                }
                if($comment->guide_id != null)
                {
                    $comment->guide= Guide::select('name' , 'image')->where('id' , $comment->guide_id)->get();
                }
            }
            return response()->json([
                'message'=>' opinion of other ',
                'data'=>$comments,

            ],200);
        }
        else{
            return response()->json([
                'message'=>'comment not found',
            ]);
        }
        }

}
