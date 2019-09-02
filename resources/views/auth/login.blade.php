@extends('layouts.front')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert @if(session('status.success')) alert-success @else alert-danger @endif alert-dismissible fade show mt-5" role="alert">
                  {{ session('status.msg') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <form class="form-signin" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            @php
                $pwd = '';
                $email = '';
            @endphp

            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        @if(!empty($logo))
                            <div class="text-center">
                                <img src="{{asset('uploads/' . $logo)}}" class="rounded-circle" alt="logo" width="100" />
                            </div>
                        @endif
                        <h5 class="card-title text-center">Log In</h5>
                        <div class="form-label-group">
                            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email Address" value="{{$email}}" required autofocus>
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
                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" value ="{{$pwd}}" required>
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

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                        <button class="btn btn-lg btn-primary btn-block text-uppercase" id="submit" type="submit">
                            Log in
                        </button>
                        <a class="btn btn-link d-block text-center" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                        @if(config('constants.enable_client_signup'))
                            <a class="btn btn-link d-block text-center py-0" href="{{ route('client.register-form') }}">
                            {{__('messages.client_register')}}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if(config('app.env') == 'demo')
        <div class="row justify-content-md-center">
            <div class="col-md-4">
                <p class="text-center text-danger">Demo Logins</p>
                <table class="table table-sm">
                    <tr>
                        <th>User Role</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>#</th>
                    </tr>
                    <tr>
                        <th>Admin</th>
                        <td>admin@example.com</td>
                        <td>123456</td>
                        <td>
                            <button class="btn btn-sm btn-success copy" data-email="admin@example.com" data-pwd="123456">Login</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Employee</th>
                        <td>employee@example.com</td>
                        <td>123456</td>
                        <td>
                            <button class="btn btn-sm btn-success copy" data-email="employee@example.com" data-pwd="123456">Login</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Customer / Client</th>
                        <td>customer@example.com</td>
                        <td>123456</td>
                        <td>
                            <button class="btn btn-sm btn-success copy" data-email="customer@example.com" data-pwd="123456">Login</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $('button.copy').click(function(){
            $('input#inputEmail').val($(this).data('email'));
            $('input#inputPassword').val($(this).data('pwd'));
            $('button#submit').trigger('click');
        });
    </script>
@endsection