@extends('layouts.app')

@section('content')
    
    <h2>Inquiry List</h2>
    <div class="row uniform">
        <div class="12u$">
            <table class="table table-striped table-hover table-responsive datatable">
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
                <tbody>
                    @foreach ($inquiries as $inquiry)
                    <tr style="@if($inquiry->priority == 'Urgent') {{ "background:#953d75;" }} @endif">
                            <td>{{ $inquiry->id }}</td>
                            <td>{{ Carbon\Carbon::parse($inquiry->created_at)->format('Y-m-d') }}</td>
                            <td> 
                                Popup
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection