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
            Please see below inquiry overview.
        </div>
        <br/>
        <div>
        <table>
            <thead>
                <tr>
                    <th>Item Number</th>
                    <th>Unit</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $details = array();
                $count = 5;
                $inquiry_details = json_decode($inquiry_details,true);
                if(count($inquiry_details) < 5){
                    $count = count($inquiry_details);
                }
                for($i = 0;$i<$count;$i++){
                    ?>
                <tr>
                    <?php
                    $inquiry_detail = $inquiry_details[$i];
                    ?>
                    <td><?php $inquiry_detail[0]['partnum']; ?></td>
                    <td><?php $inquiry_detail[1]['qty']; ?></td>
                    <td><?php $inquiry_detail[2]['unit']; ?></td>
                    <td><?php  $inquiry_detail[3]['type']; ?></td>
                    <td><?php  $inquiry_detail[4]['category']; ?></td>
                    <td><?php  $inquiry_detail[5]['detail']; ?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <br/>
        <div>
            Please click on following link to reply. On successful reply your one darham will be credited from account. <br/>
            {{ URL::to('inquiry/reply/' . $inquiry_id) }}.<br/>
        </div><br/>
        <div>
            Regards,<br/>
            Prinquiry Team
        </div>
    </body>
</html>