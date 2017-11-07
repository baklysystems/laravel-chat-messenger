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
     * @param  int  $withId
     * @return Response
     */
    public function laravelMessenger($withId)
    {
        Messenger::makeSeen(auth()->id(), $withId);
        $withUser = config('messenger.user.model', 'App\User')::findOrFail($withId);
        $messages = Messenger::messagesWith(auth()->id(), $withUser->id);
        $threads  = Messenger::threads(auth()->id());

        return view('messenger::messenger', compact('withUser', 'messages', 'threads'));
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

        $authUserId = auth()->id();
        $withId     = $request->receiverId;
        $conversation = Messenger::getConversation($authUserId, $withId);

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_one' => $authUserId,
                'user_two' => $withId
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
            'message'    => $message,
            'senderId'   => $authUserId,
            'receiverId' => $withId
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
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
            $withUser = config('messenger.user.model', 'App\User')::findOrFail($request->withUserId);
            $threads  = Messenger::threads(auth()->id());
            $view     = view('messenger::partials.threads', compact('threads', 'withUser'))->render();

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

    /**
     * Delete a message.
     *
     * @param  int  $id
     * @return Response.
     */
    public function destroy($id)
    {
        $confirm = Messenger::deleteMessage($id, auth()->id());

        if ($confirm) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false], 500);
        }
    }
}
