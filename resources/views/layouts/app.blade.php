@include('partials.header')
@include('partials.topbar')
    
    <!-- Wrapper -->
    <div id="wrapper">
        <section id="intro" class="wrapper style1 fullscreen fade-up">   
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
           
 
