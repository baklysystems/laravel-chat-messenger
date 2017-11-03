@php
$authId = auth()->id();
@endphp
@if ($messages)
    @foreach ($messages as $key => $message)
        <div class="row message-row">
            <p title="{{date('d-m-Y h:i A' ,strtotime($message->created_at))}}"
                @if ($message->sender_id === $authId)
                    class="sent"
                @else
                    class="received"
                @endif>
                {{$message->message}}
            </p>
            @if ($message->sender_id === $authId)
                <i class="fa fa-ellipsis-h fa-2x pull-right" aria-hidden="true">
                    <div class="delete" data-id="{{$message->id}}">Delete</div>
                </i>
            @else
                <i class="fa fa-ellipsis-h fa-2x pull-left" aria-hidden="true">
                    <div class="delete" data-id="{{$message->id}}">Delete</div>
                </i>
            @endif
        </div>
    @endforeach
@endif
