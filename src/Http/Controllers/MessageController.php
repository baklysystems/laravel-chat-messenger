<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaklySystems\LaravelMessenger\Messenger;
use BaklySystems\LaravelMessenger\Models\Message;
use BaklySystems\LaravelMessenger\Models\Conversation;

class MessageController extends Controller
{
    /**
     * Create a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Message::rules());

        $authUserId   = auth()->id();
        $conversation = Messenger::getConversation($authUserId, $request->receiverId);

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_one' => $authUserId,
                'user_two' => $request->receiverId
            ]);
        }

        $request = collect($request)
            ->put('conversation_id', $conversation->id)
            ->put('sender_id', $authUserId);
        $message = Message::create($request->all());

        return response()->json([
            'success' => true,
            'messgae' => $message
        ], 200);
    }
}
