<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Hi {{ $user_name }},</h2>
        <br/>
        <div>
            Inquiry replied by {{  $seller_name }}. 
        </div><br/>
        <div>
            Please click on following link to see details and compare it with other suppliers. <br/>
            {{ URL::to('inquiry/details/' . $inquiry_id) }}.<br/>
        </div><br/>
        <div>
            Regards,<br/>
            Prinquiry Team
        </div>
    </body>
</html>