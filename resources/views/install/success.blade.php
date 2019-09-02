@extends('layouts.front', ['no_header' => 1])
@section('title', 'PMS Installation')

@section('content')
<div class="container">
    <div class="row">
    	<div class="col-md-12">
        	<h3 class="text-center bg-light">{{ config('app.name', 'PMS') }} Installation</small></h3>
        	<hr/>

        	<div class="card">
            	<!-- /.box-header -->
            	<div class="card-body">
            	<h1 class="card-title">{{ config('app.name', 'PMS') }}</h1>

            	<h3 class="text-success card-title">Great!, Your application is succesfully installed.</h3>
            	<p><br><b>Username:</b> admin@admin.com<br/> <b>Password:</b> 123456</p>
            	<p><br>Login link <a href="{{route('login')}}" target="_blank">here</a></p>
            	</div>
          	<!-- /.card-body -->
          	</div>
        </div>
    </div>
</div>
@endsection