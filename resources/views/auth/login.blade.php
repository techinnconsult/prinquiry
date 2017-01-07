@extends('layouts.app')
@section('content')
    @if(Auth::guest())
                <h1>Auto Parts Inquiry</h1>
                <p><i>Send, Receive &amp; Compare <strong>Auto Parts Inquiry</strong></i>
                        It's Fast, Easy &amp; Economical
                </p>
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}
                    <div class="row uniform">
                        <div class="4u 6u$(xsmall) form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <input placeholder="E-Mail Address" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="4u$ 6u$(xsmall) form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input placeholder="Password" id="password" type="password" class="form-control" name="password" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="6u$ 12u$(xsmall)">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                        <div class="8u$ 12u$(xsmall)">
                            <ul class="actions">
                                <li>
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>
                                </li>
                                <li>
                                    <a href="{{ url('/register') }}" class="button scrolly">Register Me</a>
                                </li>
                                <li>
                                    <a href="{{ url('/password/reset') }}" class="button scrolly"> Forgot Your Password?</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
                <h2>Our Valued Partners</h2>
                <ul style="list-style:none;">
                    <li style="float:left;margin-right:3%;">
                        <img src="{{ url('prinquiry/images/') }}/jamaliautoparts.jpg" style="width: 130px;border-radius: 5px;" />
                    </li>
                    <li style="float:left;margin-right:3%;">
                        <img src="{{ url('prinquiry/images/') }}/tiba.png" style="width: 130px;border-radius: 5px;" />
                    </li>
                    <li style="float:left;margin-right:3%;">
                        <img src="{{ url('prinquiry/images/') }}/almarooftrading.png" style="width: 130px;border-radius: 5px;" />
                    </li>
                    <li style="float:left;margin-right:3%;">
                        <img src="{{ url('prinquiry/images/') }}/almaroof.png" style="width: 130px;border-radius: 5px;" />
                    </li>
                    <li style="float:left;margin-right:3%;">
                        <img src="{{ url('prinquiry/images/') }}/kuwait.png" style="width: 130px;border-radius: 5px;" />
                    </li>
                    <li style="float:left;">
                        <img src="{{ url('prinquiry/images/') }}/amp.png" style="width: 130px;border-radius: 5px;" />
                    </li>
                </ul>
    @endif
@endsection
                