@extends('layouts.front', ['no_header' => 1])
@section('title', 'PMS Installation - Check server')

@section('content')
<div class="container">
    <div class="row">
      <div class="col-md-12">
        <h3 class="text-center bg-light">{{ config('app.name', 'PMS') }} Installation <small>Step 2 of 3</small></h3>

          <hr/>
          @include('install.partials.nav', ['active' => 'app_details'])

          <div class="card">
            <!-- /.box-header -->
            <div class="card-body">

              @if(session('error'))
                <div class="alert alert-danger">
                  {!! session('error') !!}
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

              <form id="details_form" method="post" 
                      action="{{route('install.postDetails')}}">
                  {{ csrf_field() }}

                  <h4>Application Details</h4>
                  <hr/>

                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="app_name">Application Name:*</label>
                      <input type="text" class="form-control" name="APP_NAME" id="app_name" placeholder="" required>
                  </div>

                  <div class="form-group col-md-6">
                      <label for="app_title">Application Title:</label>
                      <input type="text" name="APP_TITLE" class="form-control" id="app_title">
                  </div>
                </div>
                <br/>
                <h4> License Details <small class="text-danger">Make sure to provide correct information from Envato/codecanyon</small></h4>
                <hr/>
                <div class="form-row">
                  <div class="form-group col-md-4">
                      <label for="envato_purchase_code">Envato Purchase Code:*</label>
                      <input type="password" name="ENVATO_PURCHASE_CODE" required class="form-control" id="envato_purchase_code">
                  </div>
                  
                  <div class="form-group col-md-4">
                      <label for="envato_username">Envato Username:*</label>
                      <input type="text" name="ENVATO_USERNAME" required class="form-control" id="envato_username">
                  </div>
              
                  <div class="form-group col-md-4">
                      <label for="envato_email">Your Email:</label>
                      <input type="email" name="ENVATO_EMAIL" class="form-control" id="envato_email" placeholder="optional">
                      <p class="help-block">For Newsletter & support</p>
                  </div>
                </div>
                  
                <div class="clearfix"></div>
                  
                <h4> Database Details <small class="text-muted">Make sure to provide correct information</small></h4>
                <hr/>

                <div class="form-row">
                  <div class="form-group col-md-4">
                      <label for="db_host">Database Host:*</label>
                      <input type="text" class="form-control" id="db_host" name="DB_HOST" required placeholder="localhost / 127.0.0.1">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="db_port">Database Port:*</label>
                      <input type="text" class="form-control" id="db_port" name="DB_PORT" required value="3306">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="db_database">Database Name:*</label>
                      <input type="text" class="form-control" id="db_database" name="DB_DATABASE" required>
                      <p class="help-block text-danger"><small>Name of empty database</small></p>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="db_username">Database Username:*</label>
                      <input type="text" class="form-control" id="db_username" name="DB_USERNAME" required>
                  </div>

                  <div class="form-group col-md-6">
                      <label for="db_password">Database Password:*</label>
                      <input type="password" class="form-control" id="db_password" name="DB_PASSWORD" required>
                  </div>
                </div>

                {{-- 
                <div class="clearfix"></div>
                <br/>
                <h4>Email Configuration<small class="text-muted"> Use for sending mails</small></h4>
                <hr/>

                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="MAIL_DRIVER">Send mails using:*</label>
                      <select class="form-control" name="MAIL_DRIVER" id="MAIL_DRIVER">
                        <option value="sendmail">PHP Mail</option>
                        <option value="smtp">SMTP</option>
                      </select>
                  </div>
                </div>

                <div class="clearfix"></div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="MAIL_FROM_ADDRESS">Default from address:*</label>
                      <input type="email" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" placeholder="hello@ultimatefosters.com" required>
                  </div>

                  <div class="form-group col-md-6">
                      <label for="MAIL_FROM_NAME">Default from name:</label>
                      <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" placeholder="(Optional)">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-4 smtp hide">
                      <label for="MAIL_HOST">SMTP Host:*</label>
                      <input type="text" class="form-control smtp_input" id="MAIL_HOST" name="MAIL_HOST" required disabled>
                  </div>

                  <div class="form-group col-md-4 smtp hide">
                      <label for="MAIL_PORT">SMTP Mail Port:*</label>
                      <input type="text" class="form-control smtp_input" id="MAIL_PORT" name="MAIL_PORT" required disabled>
                  </div>

                  <div class="form-group col-md-4 smtp hide">
                      <label for="MAIL_ENCRYPTION">SMTP Mail Encryption:*</label>
                      <input type="text" class="form-control smtp_input" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" required disabled placeholder="tls or ssl">
                  </div>

                  <div class="form-group col-md-6 smtp hide">
                      <label for="MAIL_USERNAME">SMTP Username:*</label>
                      <input type="text" class="form-control smtp_input" id="MAIL_USERNAME" name="MAIL_USERNAME" required disabled>
                  </div>

                  <div class="form-group col-md-6 smtp hide">
                      <label for="MAIL_PASSWORD">SMTP Password:*</label>
                      <input type="password" class="form-control smtp_input" id="MAIL_PASSWORD" name="MAIL_PASSWORD" required disabled>
                  </div>
                </div>
                --}}

                <hr/>
                  <div class="col-md-12">
                    <a href="{{route('install.index')}}" class="btn btn-default pull-left back_button" tabindex="-1">Back</a>
                    <button type="submit" id="install_button" class="btn btn-primary float-md-right">Install</button>
                  </div>

                  <div class="col-md-12 text-center text-danger install_msg hide">
                    <strong>Installation in progress, Please do not refresh, go back or close the browser.</strong>
                  </div>

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
      $('select#MAIL_DRIVER').change(function(){
        var driver = $(this).val();

        if(driver == 'smtp'){
          $('div.smtp').removeClass('hide');
          $('input.smtp_input').attr('disabled', false);
        } else {
          $('div.smtp').addClass('hide');
          $('input.smtp_input').attr('disabled', true);
        }
      });

      $('form#details_form').submit(function(){
        $('button#install_button').attr('disabled', true).text('Installing...');
        $('div.install_msg').removeClass('hide');
        $('.back_button').hide();
      });

    })
  </script>
@endsection