<?php

namespace BaklySystems\LaravelMessenger;

use BaklySystems\LaravelMessenger\Models\Message;
use BaklySystems\LaravelMessenger\Models\Conversation;

class Messenger
{
    function __construct()
    {
        //
    }

    /**
     * Check if a conversation exists between two users,
     * and return conversation (if any).
     *
     * @param  int  $authId
     * @param  int  $receiverId
     * @return collection
     */
    public static function getConversation($authId, $receiverId)
    {
        $conversation = Conversation::where(function ($query) use ($authId, $receiverId) {
                $query->whereUserOne($authId)
                      ->whereUserTwo($receiverId);
            })->orWhere(function ($query) use ($authId, $receiverId) {
                $query->whereUserOne($receiverId)
                      ->whereUserTwo($authId);
            })->first();

        return $conversation;
    }

    /**
     * Get all conversations with all users for a user.
     *
     * @param  int  $authId
     * @param  int  $take
     * @param  int  $skip
     * @return collection
     */
    public static function userConversations($authId, $take = 20, $skip = 0)
    {
        $conversation = Conversation::whereUserOne($authId)
            ->orWhere('user_two', $authId)
            ->take($take)
            ->skip($skip)
            ->get();

        return $conversation;
    }

    /**
     * Get messages between two users.
     *
     * @param  int  $loggedUserId
     * @param  int  $withUser
     * @param  int  $take
     * @param  int  $skip
     * @return collection
     */
    public static function messagesWith($loggedUserId, $withUser, $take = 20, $skip = 0)
    {
        $conversation = self::getConversation($loggedUserId, $withUser);

        if ($conversation) {
            $messages = Message::whereConversationId($conversation->id)
                ->take($take)
                ->skip($skip)
                ->get();
            return $messages;
        }

        return null;
    }

    /**
     * Get user threads with all other users.
     *
     * @param  int  $authId
     * @param  int  $take
     * @param  int  $skip
     * @return collection
     */
    public static function threads($authId, $take = 20, $skip = 0)
    {
        $conversations = self::userConversations($authId, $take, $skip);
        $threads       = [];

        foreach ($conversations as $key => $conversation) {
            if ($conversation->user_one === $authId) {
                $withUser = $conversation->userTwo;
            } else {
                $withUser = $conversation->userOne;
            }
            $collection                 = (object) null;
            $collection->conversationId = $conversation->id;
            $collection->withUser       = $withUser;
            $collection->lastMessage    = $conversation->lastMessage();
            $threads[]                  = $collection;
        }

        return collect($threads);
    }
}
