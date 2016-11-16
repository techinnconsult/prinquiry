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
     <section id="intro" class="wrapper style1 fullscreen fade-up">   
        <div class="inner">
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
        </div>
        </section> 
    @endif
@endsection
