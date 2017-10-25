<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaklySystems\LaravelMessenger\Models\Message;
use BaklySystems\LaravelMessenger\Models\Conversation;

class MessageController extends Controller
{

    /**
     * Check if a conversation exists between two users,
     * and return conversation (if any).
     *
     * @param  int  $authUserId
     * @param  int  $receiverId
     * @return collection
     */
    protected function getConversation($authUserId, $receiverId)
    {
        $conversation = new Conversation;
        $conversation->where(function ($query) use ($authUserId, $receiverId) {
                        $query->whereUserOne($authUserId)
                              ->whereUserTwo($receiverId);
                    })->orWhere(function ($query) use ($authUserId, $receiverId) {
                        $query->whereUserOne($receiverId)
                              ->whereUserTwo($authUserId);
                    })->first();

        return $conversation;
    }

    /**
     * Create a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate([
            $request,
            Message::rules()
        ]);

        $authUserId   = auth()->id();
        $conversation = $this->getConversation($authUserId, $request->receiverId);

        if (! $conversation) {
            $conversation->create([
                'user_one' => $authUserId,
                'user_two' => $request->receiverId
            ]);
        }

        $request = collect($request)
            ->put('conversation_id', $conversation->id)
            ->put('user_id', $authUserId);
        $message = Message::create($request->all());

        return response()->json([
            'success' => true,
            'messgae' => $message
        ], 200);
    }
}
