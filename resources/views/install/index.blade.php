@extends('layouts.front', ['no_header' => 1])
@section('title', 'Welcome - Installation')

@section('content')
<div class="container">
    <div class="row">
      <div class="col-md-12">
        <h3 class="text-center bg-light">{{ config('app.name', 'PMS') }} Installation <small>Step 1 of 3</small></h3>
          <hr/>
          @include('install.partials.nav', ['active' => 'install'])

          <div class="card">
            <!-- /.box-header -->
            <div class="card-body">
              <h3 class="card-title text-success">
                Welcome to Installation!
              </h3>

              <h6 class="card-subtitle mb-2"><strong class="text-danger">[IMPORTANT]</strong> Before you start installing make sure you have following information ready with you:</h6>

              <p></p>

              <ol>
                <li>
                  <b>Application Name</b> - Something short & Meaningful.
                </li>
                <br/>
                <li>
                  <b>Database informations:</b>
                  <ul>
                    <li>Username</li>
                    <li>Password</li>
                    <li>Database Name (<span class="text-">Name of empty database</span>)</li>
                    <li>Database Host</li>
                  </ul>
                </li>
                <br/>
                <li>
                  <b>Envato or Codecanyon Details:</b>
                  <ul>
                    <li><b>Envato purchase code.</b> (<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Where Is My Purchase Code?</a>)</li>
                    <li>
                      <b>Envato Username.</b> (Your envato username)
                    </li>
                  </ul>
                </li>
                <br/>
              </ol>

              @include('install.partials.e_license')
              
              <a href="{{route('install.details')}}" class="btn btn-danger float-md-right">I Agree, Let's Go!</a>
            </div>
          <!-- /.card-body -->
          </div>

        </div>

    </div>
</div>
@endsection
