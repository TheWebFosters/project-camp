@extends('layouts.front')

@section('content')
<div class="container">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-signin my-5">
            <div class="card-body">
                <h5 class="card-title text-center">
                    Reset Password
                </h5>
                <form class="form-signin" method="POST" action="{{ route('password.request') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-label-group" >
                        <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email Address" value="{{ $email or old('email') }}" required autofocus>
                        <label for="inputEmail">
                            Email Address
                        </label>
                        @if ($errors->has('email'))
                            <span class="help-block text-danger">
                                <small class="help-text">
                                    {{ $errors->first('email') }}
                                </small>
                            </span>
                        @endif
                    </div>

                    <div class="form-label-group">
                        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
                        <label for="inputPassword">
                            Password
                        </label>
                        @if ($errors->has('password'))
                            <span class="help-block text-danger">
                                <small class="help-text">
                                    {{ $errors->first('password') }}
                                </small>
                            </span>
                        @endif
                    </div>

                    <div class="form-label-group">
                        <input type="password" name="password_confirmation" id="password-confirm" class="form-control" placeholder="Confirm Password" required>
                        <label for="password-confirm">
                            Confirm Password
                        </label>
                        @if ($errors->has('password_confirmation'))
                            <span class="help-block text-danger">
                                <small class="help-text">
                                    {{ $errors->first('password_confirmation') }}
                                </small>
                            </span>
                        @endif
                    </div>

                    <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
