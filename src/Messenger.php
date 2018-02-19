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
     * @param  int  $withId
     * @return collection
     */
    public function getConversation($authId, $withId)
    {
        $conversation = Conversation::where(function ($query) use ($authId, $withId) {
                $query->whereUserOne($authId)
                      ->whereUserTwo($withId);
            })->orWhere(function ($query) use ($authId, $withId) {
                $query->whereUserOne($withId)
                      ->whereUserTwo($authId);
            })->first();

        return $conversation;
    }

    /**
     * Make a new conversation between two users.
     *
     * @param  int  $authId
     * @param  int  $withId
     * @return collection
     */
    public function newConversation($authId, $withId)
    {
        $conversation = Conversation::create([
            'user_one' => $authId,
            'user_two' => $withId
        ]);

        return $conversation;
    }

    /**
     * Get last {$take} conversations with all users for a user.
     *
     * @param  int  $authId
     * @param  int  $take  (optional)
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
     * Create a new message between two users.
     *
     * @param  int  $conversationId
     * @param  int  $senderId
     * @param  string  $message
     * @param  boolean  $isSeen  (optional)
     * @param  boolean  $deletedSender  (optional)
     * @param  boolean  $deletedReceiver  (optional)
     * @return collection
     */
    public function newMessage(
        $conversationId,
        $senderId,
        $message,
        $isSeen = 0,
        $deletedSender = 0,
        $deletedReceiver = 0
    )
    {
        $message = Message::create([
            'conversation_id'       => $conversationId,
            'sender_id'             => $senderId,
            'message'               => $message,
            'is_seen'               => $isSeen,
            'deleted_from_sender'   => $deletedSender,
            'deleted_from_receiver' => $deletedReceiver
        ]);

        return $message;
    }

    /**
     * Get last {$take} messages between two users.
     *
     * @param  int  $authId
     * @param  int  $withId
     * @param  int  $take  (optional)
     * @return collection
     */
    public function messagesWith($authId, $withId, $take = 20)
    {
        $conversation = $this->getConversation($authId, $withId);

        if ($conversation) {
            $messages = Message::whereConversationId($conversation->id)
                ->where(function ($query) use ($authId, $withId) {
                    $query->where(function ($qr) use ($authId) {
                        $qr->where('sender_id', $authId) // this message is sent by the authUser.
                            ->where('deleted_from_sender', 0);
                    })->orWhere(function ($qr) use ($withId) {
                        $qr->where('sender_id', $withId) // this message is sent by the receiver/withUser.
                            ->where('deleted_from_receiver', 0);
                    });
                })
                ->take($take)
                ->get();

            return $messages;
        }

        return collect();
    }

    /**
     * Get last {$take} user threads with all other users.
     *
     * @param  int  $authId
     * @param  int  $take  (optional)
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
            return $ins['lastMessage']['created_at'];
        });

        return $threads->values()->all();
    }

    /**
     * Make messages seen for a conversation.
     *
     * @param  int  $authId
     * @param  int  $withId
     * @return boolean
     */
    public function makeSeen($authId, $withId)
    {
        $conversation = $this->getConversation($authId, $withId);
        if ($conversation) {
            Message::whereConversationId($conversation->id)->update([
                'is_seen' => 1
            ]);
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Delete a message.
     *
     * @param  int  $messageId
     * @param  int  $authId
     * @return boolean.
     */
    public function deleteMessage($messageId, $authId)
    {
        $message = Message::findOrFail($messageId);
        if ($message->sender_id == $authId) {
            $message->update(['deleted_from_sender' => 1]);
        } else {
            $message->update(['deleted_from_receiver' => 1]);
        }

        return response()->json(['success' => true], 200);
    }
}
