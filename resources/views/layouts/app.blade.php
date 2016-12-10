@include('partials.header')
@include('partials.topbar')
    
    <!-- Wrapper -->
    <div id="wrapper">
        <section id="intro" class="wrapper style1 fullscreen fade-up">   
            <div class="inner">
                 <div class="12u$" style="text-align: right;">
                     <h3 class="title"><a href="{{ url('/home') }}" class="title"> Dashboard </a> </h3>
                 </div>
            </div>
            <div style="clear:both"></div>
            <div class="inner">
                @if (Session::has('message'))
                    <div class="note note-info">
                        <p>{{ Session::get('message') }}</p>
                    </div>
                @endif
                
                @include('flash::message')
                
                @yield('content')
            </div>
        </section>
        <section id="contact-us" class="wrapper style3 fade-up">
            @include('partials.contactus')
        </section>
    </div>
    
@include('partials.footer')
           
 
