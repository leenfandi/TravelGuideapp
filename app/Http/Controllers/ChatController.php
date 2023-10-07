<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Http\Requests\GetChatRequest;
use App\Http\Requests\StoreChatRequest;

class ChatController extends Controller
{
    public function indexmessage(GetChatRequest $request){

        $data = $requset->validated();

        $isPrivate = 1;
        if($request ->has('is_private')) {
            $isPrivate = (int)$data['is_private'];
        }

        $chat = Chat::where('is_private',$isPrivat)
        ->hasParticipant(Auth::guard('api')->id())
        ->whereHas( 'message')
        ->with('lastmessage.user','participants.user' )
        ->latest('updated_at')
        ->get();

        return response()->json([
            'message'=>'success',
            'رسالتك وصلت' => $chat,

        ],201);

    }
    public function showmessage(chat $chat){
        $chat->load('lastmessage.user','participants.user');

        return response()->json([
            'message'=>'success',
            'رسالتك '=> $chat,

        ],201);
    }
    public function store(StoreChatRequest $request){

        $data = $this->prepareStoreData($request);

        if($data['userId'] == $data['otheruserid']){

            return response()->json([
                'message'=>'fault',

            ],400);
        }

        $previouschat = $this->getPreviousChat($data['otheruserid']);
        if ( $previouschat == null){

            $chat = Chat::create($data['data']);
            $chat->participants()->createMany([

                [
                     'user_id' => $data['userid']


                ],
                [
                    'user_id' => $data['otheruserid']
                ]
            ]);
                   $chat->refresh()->load('lastmessage.user','participants.user');

                   return response()->json([
                    'message'=>$chat,

                ],201);

    }
                return response()->json([
                  'Success'=> $previouschat->load('lastmessage.user','participants.user'),

    ],200);
    }
    

    private function getPreviousChat( int $otheruserId) {

        $userid =Auth::guard('api')->id();

        return Chat::where('is_private',1)
        ->whereHas('participants' , function($query) use ($userid){

            $query->where('user_id',$userid);
        })

        ->whereHas('participants' , function($query) use ($otheruserId){

            $query->where('user_id',$otheruserId);
        })
               ->first();

    }

    private function prepareStoreData(StoreChatRequest $request){

        $data = $requset->validated();

        $otheruserId = (int)$data['user_id'];
        unset($data['user_id']);
        $data['created_by'] =Auth::guard('api')->id();

        return response()->json([
            'otheruserid'=>$otheruserId,
            'userId' =>  Auth::guard('api')->id() ,
            'data' =>$data

              ],201);
    }
}
