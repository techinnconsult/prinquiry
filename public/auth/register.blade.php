@extends('layouts.app')
@section('content')
    @if (Auth::guest())
     <section id="intro" class="wrapper style1 fullscreen fade-up">   
        <div class="inner">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                {{ csrf_field() }}
                <div class="row uniform">
                    <div class="6u 6u$(xsmall) form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                        <input placeholder="Company Name" id="company_name" type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" autofocus>
                        @if ($errors->has('company_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('company_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="6u 6u$(xsmall) form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <input placeholder="Contact Person" id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="6u 6u$(xsmall) form-group{{ $errors->has('mobile_phone') ? ' has-error' : '' }}">
                        <input placeholder="Mobile Phone" id="mobile_phone" type="text" class="form-control" name="mobile_phone" value="{{ old('mobile_phone') }}" autofocus>
                        @if ($errors->has('mobile_phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mobile_phone') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="6u 6u$(xsmall) form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <input placeholder="E-Mail Address" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="6u 6u$(xsmall) form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input placeholder="Password" id="password" type="password" class="form-control" name="password" required>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="6u 6u$(xsmall) form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <input placeholder="Confirm Password" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="12u$ form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Register
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
     </section>
    @endif
@endsection               