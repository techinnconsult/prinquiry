<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Hi {{ $seller_name }},</h2>
        <br/>
        <div>
            New Inquiry created by {{ $user_name }}.Inquiry prority is <b>{{ $priority }}</b>. Client location is {{ $location }}. 
        </div>
        <br/>
        <div>
            Please click on following link to reply. On successful reply your one darham will be credited from account. <br/>
            {{ URL::to('inquiry/reply/' . $inquiry_id) }}.<br/>
        </div><br/>
        <div>
            Regards,<br/>
            AutoPartsInquiry.com
        </div>
    </body>
</html>