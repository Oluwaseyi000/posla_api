<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\ChatMessageSentEvent;
use App\Http\Requests\PostMessageRequest;

class ChatController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function fetchMessages(User $receiver)
    {
        $chats = ChatMessage::where(['user_id' => $this->getAuthUser()->id, 'receiver_id' => $receiver->id])
            ->select([ 'id', 'message', 'receiver_id', 'created_at'])
            ->orderBy('id', 'desc')->get();
        return $this->successResponse($chats);

    }


    public function sendMessage(PostMessageRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->getAuthUser();

        $message = $user->messages()->create([
            'message' => $request->message,
            'receiver_id' => $request->receiver_id
        ]);

        broadcast(new ChatMessageSentEvent($message))->toOthers();
        return $this->successResponse($message);
    }


}
