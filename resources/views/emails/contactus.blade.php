<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            {{  $user_name }} Contacts you from Autoparts Inquiry. 
        </div><br/>
        <div>
            You can contact them via <a href='mailto:{{ $user_email }}'> email </a>
        </div><br/>
        <div>
            Please see below user message. <br/>
            {{ $user_message }}.<br/>
        </div>
    </body>
</html>