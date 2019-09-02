@extends('layouts.auth', ['no_header' => 1])
@section('title', 'POS Installation - Check server')

@section('content')
<div class="container">
    <div class="row">
        <h3 class="text-center">{{ config('app.name', 'POS') }} Installation <small>Step 2 of 3</small></h3>

        <div class="col-md-8 col-md-offset-2">
          <hr/>
          @include('install.partials.nav', ['active' => 'server'])

          <div class="box box-primary active" id="Installation">
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table">
                <tr>
                  <td>PHP >= 7.1</td>
                  <td>
                    @if($output['php'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>OpenSSL PHP Extension</td>
                  <td>
                    @if($output['openssl'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>PDO PHP Extension</td>
                  <td>
                    @if($output['pdo'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>Mbstring PHP Extension</td>
                  <td>
                    @if($output['mbstring'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>Tokenizer PHP Extension</td>
                  <td>
                    @if($output['tokenizer'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>XML PHP Extension</td>
                  <td>
                    @if($output['xml'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>cURL PHP Extension</td>
                  <td>
                    @if($output['curl'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>zip PHP Extension</td>
                  <td>
                    @if($output['zip'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td>gd PHP Extension</td>
                  <td>
                    @if($output['gd'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>

                <tr>
                  <td><b>{{storage_path()}}</b> is writable?</td>
                  <td>
                    @if($output['storage_writable'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>

                <tr>
                  <td><b>{{base_path('bootstrap/cache')}}</b> is writable?</td>
                  <td>
                    @if($output['cache_writable'])
                      <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                    @else
                      <i class="fa fa-close text-danger" aria-hidden="true"></i>
                    @endif
                  </td>
                </tr>
                      
            </table>

              <br/>
              <a href="{{route('install.index')}}" class="btn btn-default pull-left">Back</a>

              <a @if($output['next']) href="{{route('install.details')}}" @endif class="btn btn-primary pull-right @if(!$output['next']) disabled-link @endif" @if(!$output['next']) disabled onclick="return false;" @endif>Next</a>
            </div>
          <!-- /.box-body -->
          </div>
        </div>

    </div>
</div>
@endsection