@php
$authId = auth()->id();
@endphp
@if ($messages)
    @foreach ($messages as $key => $message)
        <div class="row">
            <p title="{{date('d-m-Y h:i A' ,strtotime($message->created_at))}}"
                @if ($message->sender_id === $authId)
                    class="sent"
                @else
                    class="received"
                @endif>
                {{$message->message}}
            </p>
        </div>
    @endforeach
@endif
