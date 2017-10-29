<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use Illuminate\Http\Request;
use BaklySystems\LaravelMessenger\Models\Message;
use BaklySystems\LaravelMessenger\Facades\Messenger;
use BaklySystems\LaravelMessenger\Models\Conversation;

class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }

    /**
     * Get messenger page.
     *
     * @param  int  $userId
     * @return Response
     */
    public function laravelMessenger($userId)
    {
        $user     = config('messenger.user.model', 'App\User')::findOrFail($userId);
        $messages = Messenger::messagesWith(auth()->id(), $user->id);
        $threads  = Messenger::threads(auth()->id());

        return view('messenger::messenger', compact('user', 'messages', 'threads'));
    }
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
        $receiverId   = $request->receiverId;
        $conversation = Messenger::getConversation($authUserId, $receiverId);

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_one' => $authUserId,
                'user_two' => $receiverId
            ]);
        }

        $request = collect($request)
            ->put('conversation_id', $conversation->id)
            ->put('sender_id', $authUserId);
        $message = Message::create($request->all());

        // Pusher
        $pusher = new Pusher(
            config('messenger.pusher.app_key'),
            config('messenger.pusher.app_secret'),
            config('messenger.pusher.app_id'),
            [
                'cluster' => config('messenger.pusher.options.cluster')
            ]
        );
        $pusher->trigger('messenger-channel', 'messenger-event', [
            'message'    => $message->message,
            'senderId'   => $authUserId,
            'receiverId' => $receiverId
        ]);

        return response()->json([
            'success' => true,
            'messgae' => $message
        ], 200);
    }

    /**
     * Load threads view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response.
     */
    public function loadThreads(Request $request)
    {
        if ($request->ajax()) {
            $threads  = Messenger::threads(auth()->id());
            $view     = view('messenger::partials.threads', compact('threads'))->render();

            return response()->json($view, 200);
        }
    }

    /**
     * Load more messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response.
     */
    public function moreMessages(Request $request)
    {
        $this->validate($request, ['receiverId' => 'required|integer']);

        if ($request->ajax()) {
            $messages = Messenger::messagesWith(
                auth()->id(),
                $request->receiverId,
                $request->take
            );
            $view = view('messenger::partials.messages', compact('messages'))->render();

            return response()->json([
                'view'          => $view,
                'messagesCount' => $messages->count()
            ], 200);
        }
    }
}
