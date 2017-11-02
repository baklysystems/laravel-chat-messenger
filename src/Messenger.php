<?php

/**
 * IoC Messenger
 *
 * @author Mohamed Abdul-Fattah
 * @license MIT
 */

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
    public function getConversation($authId, $receiverId)
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
     * Get last {$take} conversations with all users for a user.
     *
     * @param  int  $authId
     * @param  int  $take
     * @return collection
     */
    public function userConversations($authId, $take = 20)
    {
        $collection    = Conversation::whereUserOne($authId)
            ->orWhere('user_two', $authId);
        $totalRecords  = $collection->count();
        $conversations = $collection->take($take)
            ->skip($totalRecords - $take)
            ->get();

        return $conversations;
    }

    /**
     * Get last {$take} messages between two users.
     *
     * @param  int  $loggedUserId
     * @param  int  $withUser
     * @param  int  $take
     * @return collection
     */
    public function messagesWith($loggedUserId, $withUser, $take = 20)
    {
        $conversation = $this->getConversation($loggedUserId, $withUser);

        if ($conversation) {
            $collection   = Message::whereConversationId($conversation->id);
            $totalRecords = $collection->count();
            $messages     = $collection->take($take)
                ->skip($totalRecords - $take)
                ->get();

            return $messages;
        }

        return null;
    }

    /**
     * Get last {$take} user threads with all other users.
     *
     * @param  int  $authId
     * @param  int  $take
     * @return collection
     */
    public function threads($authId, $take = 20)
    {
        $conversations = $this->userConversations($authId, $take);
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

        $threads = collect($threads);
        $threads = $threads->sortByDesc(function ($ins, $key) { // order threads by last updated message.
            $ins = (array) $ins;
            return $ins['lastMessage']['updated_at'];
        });

        return $threads->values()->all();
    }
}
