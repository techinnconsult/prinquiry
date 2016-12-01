@extends('layouts.app')

@section('content')
    
    <h2>Inquiry Received</h2>
    <div class="row uniform">
        <div class="12u$">
            <table class="table table-striped table-hover table-responsive datatable">
                <thead>
                    <tr>
                        <th>Inquiry #</th>
                        <th>Date</th>
                        <th>Customer Name</th>
                        <th>Priority</th>
                        <th>Location</th>
                        <th>Inquiry For</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <!--<th>&nbsp;</th>-->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inquiries as $inquiry)
                    <tr style="@if($inquiry->priority == 'Urgent' && $inquiry->status == 'New') {{ "background:#953d75;" }} @endif">
                            <td>{{ $inquiry->id }}</td>
                            <td>{{ Carbon\Carbon::parse($inquiry->created_at)->format('Y-m-d') }}</td>
                            <td> 
                                @foreach($customers as $customer)
                                    @if($customer->id == $inquiry->customer_id)
                                   {{ $customer->name }}
                                   @endif
                                @endforeach
                            </td>
                            <td>{{ $inquiry->priority }}</td>
                            <td>{{ $inquiry->location }}</td>
                            <td>{{ "Pop Up" }}</td>
                            <td>{{ $inquiry->remarks }}</td>
                            <td>
                                @if($inquiry->status == 'Reply')
                                    {{ 'Replied' }}
                                @else
                                    {{ $inquiry->status }}
                                @endif
                            </td>
                            <td>
                                <ul class="actions">
                                    <li><a href='{{ URL::to('inquiry/reply/' . $inquiry->id) }}' class="btn btn-primary">Reply</a></li>
                                    <li><a onclick="deleted()" href='#' class="btn btn-primary">Delete</a></li>
                                    <li><a onclick="closed()" href='#' class="btn btn-primary">Close</a></li>
                                </ul>
                                <script>
                                function closed() {
                                    var r = confirm("Are you sure you want to close this inquiry?");
                                    if (r == true) {
                                         window.location = "{{ URL::to('inquiry/closeSellerInquiry/' . $inquiry->id) }}";
                                    } 
                                }
                                
                                function deleted() {
                                    var r = confirm("Are you sure you want to delete this inquiry?");
                                    if (r == true) {
                                         window.location = "{{ URL::to('inquiry/deleteSellerInquiry/' . $inquiry->id) }}";
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
