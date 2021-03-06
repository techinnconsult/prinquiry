<div class="page-header navbar navbar-fixed-top" style="display: none;">
    <div class="page-header-inner">
        <div class="page-header-inner">
            <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'PRinquiry') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
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
                </ul>
            </div>
        </div>
    </nav>
        </div>
    </div>
</div>
<!-- Sidebar -->
<section id="sidebar">
       <div class="row uniform">
            <div class="3u 12u$(xlarge) 12u$(xsmall)">
                <h2 style="margin-top: 6px;"><a href="{{ url('/home') }}" class="title">PRinquiry</a></h2>
            </div>
            <div class="6u 12u$(xlarge) 12u$(xsmall)">
                <nav id="primary_nav_wrap">
                    <ul>
                            <li>
                                <a href="{{ url('/inquiries') }}">Inquiries</a>
                                <ul>
                                    <li><a href="#">Deep MAGenu 1</a>
                                    <li><a href="#">Deep MAGenu 1</a>
                                </ul>
                            </li>
                            <li><a href="#one">Who we are</a></li>
                    </ul>
                    <div style="clear:both"></div>
                </nav>
            </div>
           <div class="3u 12u$(xlarge) 12u$(xsmall)">
               <nav>
                    <ul>
                         @if(Auth::guest())
                            <li style="float:left;"><a href="{{ url('/login') }}">Login</a></li>
                            <li style="float:left;"><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li style="float:left">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>
                            </li>
                            <li style="float:left;">
                                <a href="{{ url('/logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        @endif
                        
                    </ul>
                </nav>
               <div style="clear:both"></div>
           </div>
            <div style="clear:both;height:5px;"></div>
        </div>
</section>
