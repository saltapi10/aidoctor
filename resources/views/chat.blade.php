<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat in Laravel | Code with John</title>
    <link rel="icon" href=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <!-- End JavaScript -->

    <!-- CSS -->
    <link rel="stylesheet" href="/style.css">
    <!-- End CSS -->

</head>

<body>
<div class="chat">

    <!-- Header -->
    <div class="top">
{{--        <img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">--}}
        <div>
            <p>John Saltapidas</p>
            <small>Online</small>
        </div>
    </div>
    <!-- End Header -->

    <!-- Chat -->
    <div class="messages">
        <div class="left message">
{{--            <img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">--}}
            <p>Start chatting with Gemini below!!</p>
        </div>
    </div>
    <!-- End Chat -->

    <!-- Footer -->
    <div class="bottom">
        <label for="file">Choose medical report (PDF, DOCX, TXT):</label>
        <form enctype="multipart/form-data">
            <input type="file" name="file" id="file">
            <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
            <button type="submit"></button>
        </form>
    </div>
    <!-- End Footer -->

</div>
</body>

<script>
    //Broadcast messages
    $("form").submit(function (event) {
        event.preventDefault();

        //Stop empty messages
        if ($("form #message").val().trim() === '') {
            return;
        }

        var formData = new FormData(this);

        //Disable form
        $("form #message").prop('disabled', true);
        $("form button").prop('disabled', true);

        $.ajax({
            url: "/chat-answer-lara",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            // data: {
            //     "content": $("form #message").val(),
            //     "file": uploadfile ?? null,
            // }
            data: formData,
            contentType: false,  // Required for FormData
            processData: false,  // Required for FormData
        }).done(function (res) {

            //Populate sending message
            $(".messages > .message").last().after('<div class="right message">' +
                '<p>' + $("form #message").val() + '</p>' +
                //'<img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">' +
                '</div>');

            //Populate receiving message
            $(".messages > .message").last().after('<div class="left message">' +
                //'<img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">' +
                '<p>' + res + '</p>' +
                '</div>');

            //Cleanup
            $("form #message").val('');
            $(document).scrollTop($(document).height());

            //Enable form
            $("form #message").prop('disabled', false);
            $("form button").prop('disabled', false);
        });
    });

</script>
</html>
