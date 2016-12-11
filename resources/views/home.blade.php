@extends('layouts.app')
@section('content')
    @if(Auth::guest())
<!--        <section id="intro" class="wrapper style1 fullscreen fade-up">   
            <div class="inner">
               @include('auth.login')
           </div>
        </section> 
        <section id="one" class="wrapper style2 fullscreen spotlights"> 
            <div class="inner"> @include('auth.register') </div>
        </section>-->
     @else
        <div class="split style1">
            <section>
                <div class="row uniform">
                    <div class="12u$">
                        <h3>Inquiries Sent</h3>
                        @if(count($sentInquiry) > 0)
                            <table class="table table-striped table-hover table-responsive datatable">
                                <thead>
                                    <tr>
                                        <th>Inquiry #</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Priority</th>
                                        <th>Replies Count</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                        <!--<th>&nbsp;</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sentInquiry as $inquiry)
                                        <tr>
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
                                            <td>{{ $inquiry->count_reply }}</td>
                                            <td>{{ $inquiry->location }}</td>
                                            <td><ul class="actions">
                                                    <li><a href='{{ URL::to('inquiry/details/' . $inquiry->id) }}' class="btn btn-primary">Inquiry Details</a></li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>
                               No inquires created.  
                            </p>
                        @endif
                    </div>
                    <div style="clear:both;height:30px"></div>
                    <div class="12u$">
                        <h3>Inquiries Received</h3>
                        @if(count($inquiries) > 0)
                            <table class="table table-striped table-hover table-responsive datatable">
                                <thead>
                                    <tr>
                                        <th>Inquiry #</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Priority</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                        <!--<th>&nbsp;</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inquiries as $inquiry)
                                    <tr>
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
                                            <td>
                                                @if($inquiry->status == 'Reply')
                                                    {{ 'Replied' }}
                                                @else
                                                    {{ $inquiry->status }}
                                                @endif
                                            </td>
                                            <td><ul class="actions">
                                                    <li><a href='{{ URL::to('inquiry/reply/' . $inquiry->id) }}' class="btn btn-primary">Reply</a></li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>
                               No inquires received.  
                            </p>
                        @endif
                    </div>
                </div>
            </section>
            <section>
                @include('partials.sidebar')
            </section>
        </div>
        <li style="display:none;" class="dropdown">
           <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
               {{ Auth::user()->name }} <span class="caret"></span>
           </a>

           <ul class="dropdown-menu" role="menu">
               <li>
                   <a href="{{ url('/logout') }}"
                       onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                       Logout
                   </a>

                   <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                       {{ csrf_field() }}
                   </form>
               </li>
           </ul>
        </li>
    @endif
@endsection
