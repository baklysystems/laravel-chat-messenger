/**
 * This JS file applies only for messenger page.
 */
;(function () {
    var take       = (messagesCount < 20) ? 0 : 20,
        $messenger = $('.messenger'),
        $loader    = $('#messages-preloader'),
        channel    = pusher.subscribe('messenger-channel');

    /**
     * Do action when a message event is triggered.
     */
    channel.bind('messenger-event', function (data) {
        if (data.senderId == receiverId && data.receiverId == authId && data.receiverId != data.senderId) { // current conversation thread.
            newMessage(data.message, 'received');
            playTweet();
            loadThreads();
        } else if (data.receiverId == authId && data.receiverId != data.senderId) { // not opened thread.
            playTweet();
            loadThreads();
        }
    });

    /**
     * Append a new message to chat body.
     */
    function newMessage(message, messageClass, failed = 0) {
        $('.messenger-body').append('\
            <div class="row">\
                <p class="' + messageClass + '">' + message + '</p>\
            </div>\
        ');
        if (failed) {
            $('.messenger-body').append('\
                <a class="unsent">\
                    <small>\
                        This message didn\'t send. Check your internet connection and try again.\
                    </small>\
                </a><br>\
            ');
        } else {
            $('#message-body').val('');
        }
        scrollMessagesDown();
    }

    /**
     * Scroll messages down to some height or bottom.
     */
    function scrollMessagesDown(height = 0) {
        var scrollTo = height || $messenger.prop('scrollHeight');

        $messenger.scrollTop(scrollTo);
    }

    /**
     * Reload threads.
     */
    function loadThreads() {
        $.ajax({
            url: '/messenger/threads',
            method: 'GET'
        }).done(function (view) {
            $('.threads').html(view);
        });
    }

    /**
     * Load more messages.
     */
    function loadMessages() {
        $.ajax({
            url: '/messenger/more/messages',
            method: 'GET',
            data: {
                receiverId: receiverId,
                take: take
            }
        }).done(function (res) {
            var prevHeight = $messenger.prop('scrollHeight');

            $('.messenger-body').html(res.view);
            var newHeight  = $messenger.prop('scrollHeight');
            scrollMessagesDown(newHeight - prevHeight); // stop at the current height.
            if (res.messagesCount < take) { // load no more messages.
                take = 0;
                $loader.after('<p class="start-conv">Conversation started</p>');
                $loader.remove();
            }
        });
    }

    /**
     * Play message notification sound.
     */
    function playTweet() {
        var audio = new Audio('/vendor/messenger/sounds/tweet.mp3');
        audio.play();
    }

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        scrollMessagesDown();

        /**
         * Send message to backend and handle responses.
         */
        $(document).on('click', '#send-btn', function (e) {
            var message = $('#message-body').val();

            if (message) {
                var JqHXR = $.ajax({
                    url: '/messenger/send',
                    method: 'POST',
                    data: {
                        message: message,
                        receiverId: receiverId
                    }
                });
            }
            JqHXR.done(function (res) { // message sent.
                if (res.success) {
                    newMessage(message, 'sent');
                    loadThreads();
                }
            });
            JqHXR.fail(function (res) { // message didn't send.
                newMessage(message, 'sent', true);
            });
        });

        /**
         * Load more messages when scroll to top.
         */
        $messenger.on('scroll', function (e) {
            if (!$messenger.scrollTop() && take) {
                take += 20;
                loadMessages();
            }
        });
    });
}());
