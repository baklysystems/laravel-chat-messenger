<div class="panel panel-default">
    <div class="panel-heading"><h4>Threads</h4></div>

    <div class="panel-body">
        @foreach ($threads as $key => $thread)
            <div class="row">
                <p class="thread-user">
                    <a href="/messenger/t/{{$thread->withUser->id}}">
                        {{$thread->withUser->name}}
                    </a>
                </p>
                <p class="thread-message">
                    @if ($thread->lastMessage->sender_id === auth()->id())
                        <i class="fa fa-reply" aria-hidden="true"></i>
                    @endif
                    {{substr($thread->lastMessage->message, 0, 20)}}
                </p>
            </div>
        @endforeach
    </div>
</div>
