@extends('layouts.app')

@section('content')
    
    <h2>Inquiry List</h2>
    <div class="row uniform">
        <div class="12u$">
            <table id="supplier-list" class="table table-striped table-hover table-responsive">
                <thead>
                    <tr>
                        <th>Inquiry #</th>
                        <th>Date</th>
                        <th>Supplier List</th>
                        <th>Priority</th>
                        <th>Location</th>
                        <th>Remarks</th>
                        <th>Reply Count</th>
                        <th>Actions</th>
                        <!--<th>&nbsp;</th>-->
                    </tr>
                </thead>
                <tbody
                    @foreach ($inquiries as $inquiry)
                    <tr style="@if($inquiry->priority == 'Urgent') {{ "background:#953d75;" }} @endif">
                            <td>{{ $inquiry->id }}</td>
                            <td>{{ Carbon\Carbon::parse($inquiry->created_at)->format('Y-m-d') }}</td>
                            <td> 
                                <a id="myBtn<?php echo $inquiry->id; ?>" href="#supplier-list">Supplier List</a>
                            </td>
                            <td>{{ $inquiry->priority }}</td>
                            <td>{{ $inquiry->location }}</td>
                            <td>{{ $inquiry->remarks }}</td>
                            <td style="text-align:center">{{ $inquiry->count_reply }}</td>
                            <td>
                                <ul class="actions">
                                    <li><a href='{{ URL::to('inquiry/details/' . $inquiry->id) }}' class="btn btn-primary">Inquiry Details</a></li>
                                    <li><a onclick="deleted{{ $inquiry->id }}()" href='#' class="btn btn-primary">Delete</a></li>
                                    <li><a onclick="closed{{ $inquiry->id }}()" href='#' class="btn btn-primary">Close</a></li>
                                </ul>
                                <script>
                                    function closed{{ $inquiry->id }}() {
                                        var r = confirm("Are you sure you want to close this inquiry?");
                                        if (r == true) {
                                             window.location = "{{ URL::to('inquiry/closeInquiry/' . $inquiry->id) }}";
                                        } 
                                    }

                                    function deleted{{ $inquiry->id }}() {
                                        var r = confirm("Are you sure you want to delete this inquiry?");
                                        if (r == true) {
                                             window.location = "{{ URL::to('inquiry/deleteInquiry/' . $inquiry->id) }}";
                                        } 
                                    }
                                </script>
                            </td>
                        </tr>
                        <script>
                            // Get the modal
                            var modal = document.getElementById('myModal');

                            // Get the button that opens the modal
                            var btn = document.getElementById("myBtn<?php echo $inquiry->id; ?>");

                            // Get the <span> element that closes the modal
                            var span = document.getElementsByClassName("close")[0];

                            // When the user clicks the button, open the modal 
                            btn.onclick = function() {
                                $('#modal-title').html('Supplier list for inquiry# <?php echo $inquiry->id; ?>')
                                $.ajax({
                                    type: "GET",
                                    dataType: 'json',
                                    url: '<?php echo URL::to('/inquiry/supplier/'.$inquiry->id); ?>',
                                    success: function( msg ) {
                                        $('#myModal tbody').html("");
                                        var	rows = '';
                                        $.each( msg, function( key, value ) {
                                                rows = rows + '<tr>';
                                                rows = rows + '<td>'+value.name+'</td>';
                                                rows = rows + '<td>'+value.status+'</td>';
                                                if(value.delievery_date){
                                                    rows = rows + '<td>'+value.delievery_date+'</td>';
                                                }else{
                                                    rows = rows + '<td>N/A</td>';
                                                }
                                                rows = rows + '</tr>';
                                        });
                                        console.log(rows);
                                        $('#myModal tbody').html(rows);
                                    }
                                });
                                modal.style.display = "block";
                            }

                            // When the user clicks on <span> (x), close the modal
                            span.onclick = function() {
                                modal.style.display = "none";
                            }

                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            }
                        </script>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background-color: #493382;
            margin: auto;
            padding: 0;
            /*border: 1px solid #888;*/
            width: 55%;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.4s;
            animation-name: animatetop;
            animation-duration: 0.4s
        }

        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {top:-300px; opacity:0} 
            to {top:0; opacity:1}
        }

        @keyframes animatetop {
            from {top:-300px; opacity:0}
            to {top:0; opacity:1}
        }

        /* The Close Button */
        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding: 2px 16px;
            background-color: #312450;
            color: white;
        }

        /*.modal-body {padding: 2px 16px;}*/

        .modal-footer {
            padding: 2px 16px;
            background-color: #312450;
            color: white;
        }
    </style>
@endsection

<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <span class="close">×</span>
        <h2 id="modal-title">Modal Header</h2>
      </div>
      <div class="modal-body">
        <table class="table table-striped table-hover table-responsive">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Status</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
        </table>
      </div>
    </div>

  </div>