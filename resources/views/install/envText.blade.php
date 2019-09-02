@extends('layouts.front', ['no_header' => 1])
@section('title', 'PMS Installation')

@section('content')
<div class="container">
    <div class="row">
      <div class="col-md-12">
        <h3 class="text-center bg-light">{{ config('app.name', 'PMS') }} Installation</small></h3>

          <hr/>
          @include('install.partials.nav', ['active' => 'app_details'])

          <div class="card">
            <!-- /.box-header -->
            <div class="card-body">

              @if(session('error'))
                <div class="alert alert-danger">
                  {{ session('error') }}
                </div>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                  </ul>
                </div>
              @endif

              <form class="form" method="post" 
                    action="{{route('install.installAlternate')}}" 
                    id="env_details_form">
                  {{ csrf_field() }}

                  <h4 class="install_instuction">Hey, I need your help. </h4>
                  <p class="install_instuction">
                    Please create a file with name <code>.env</code> in application folder with <code>read & write permission</code> and paste the below content. <br/> Press install after it.
                  </p>
                  <hr/>

                  <div class="col-md-12">
                    <div class="form-group">
                        <textarea rows="25" cols="50">{{$envContent}}</textarea>
                    </div>
                  </div>
                  
                  <div class="col-md-12">
                    <button type="submit" class="btn btn-primary float-md-right" id="install_button">Install</button>
                  </div>

                  <div class="col-md-12 text-center text-danger install_msg hide">
                    <h3>Installation in progress, Please do not refresh, go back or close the browser.</strong>
                  </h3>
              </form>
            </div>
          <!-- /.card-body -->
          </div>
        </div>

    </div>
</div>
@endsection

@section('javascript')
  <script type="text/javascript">
    $(document).ready(function(){

      $('form#env_details_form').submit(function(){
        $('button#install_button').attr('disabled', true).text('Installing...');
        $(".install_instuction").addClass('hide');
        $('div.install_msg').removeClass('hide');
        $('textarea').addClass('hide');
        $('.back_button').hide();
      });

    })
  </script>
@endsection