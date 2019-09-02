@extends('layouts.front')

@section('content')
<div class="container">
    <div class="row">
        <form class="form-signin" method="POST" action="{{ route('client.register') }}">
            {{ csrf_field() }}


            <div class="col-md-12">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <a class="btn btn-link float-right py-0" href="{{ route('login') }}">
                            {{__('messages.login')}}
                        </a>
                        <h5 class="card-title text-center">@lang('messages.company_info')</h5>
                    </div>

                    <div class="row mx-0">
                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="company" name="company" class="form-control" placeholder="{{__('messages.company')}}" value="{{old('company')}}" required autofocus>
                                <label for="company">
                                    {{__('messages.company')}}
                                </label>
                                @if ($errors->has('company'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('company') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="tax_number" name="tax_number" class="form-control" placeholder="{{__('messages.tax_number')}}" value="{{old('tax_number')}}" >
                                <label for="tax_number">
                                    {{__('messages.tax_number')}}
                                </label>
                                @if ($errors->has('tax_number'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('tax_number') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="{{__('messages.mobile')}}" value="{{old('mobile')}}" required>
                                <label for="mobile">
                                    {{__('messages.mobile')}}
                                </label>
                                @if ($errors->has('mobile'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('mobile') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="alternate_contact_no" name="alternate_contact_no" class="form-control" placeholder="{{__('messages.alternate_num')}}" value="{{old('alternate_contact_no')}}">
                                <label for="alternate_contact_no">
                                    {{__('messages.alternate_num')}}
                                </label>
                                @if ($errors->has('alternate_contact_no'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('alternate_contact_no') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <select name="currency_id" class="form-control">
                                    <option value="">{{__('messages.currency')}}</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{$currency['id']}}" @if(old('currency_id') == $currency['id']) {{'selected'}} @endif>{{$currency['currency']}}</option>
                                    @endforeach
                                </select>
                                
                                @if ($errors->has('currency_id'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('currency_id') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="email" id="email" name="email" class="form-control" placeholder="{{__('messages.email')}}" value="{{old('email')}}" required>
                                <label for="email">
                                    {{__('messages.email')}}
                                </label>
                                @if ($errors->has('email'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('email') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="website" name="website" class="form-control" placeholder="{{__('messages.website')}}" value="{{old('website')}}">
                                <label for="website">
                                    {{__('messages.website')}}
                                </label>
                                @if ($errors->has('website'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('website') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="city" name="city" class="form-control" placeholder="{{__('messages.city')}}" value="{{old('city')}}">
                                <label for="city">
                                    {{__('messages.city')}}
                                </label>
                                @if ($errors->has('city'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('city') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="state" name="state" class="form-control" placeholder="{{__('messages.state')}}" value="{{old('state')}}">
                                <label for="state">
                                    {{__('messages.state')}}
                                </label>
                                @if ($errors->has('state'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('state') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="country" name="country" class="form-control" placeholder="{{__('messages.country')}}" value="{{old('country')}}">
                                <label for="country">
                                    {{__('messages.country')}}
                                </label>
                                @if ($errors->has('country'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('country') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-label-group">
                                <input type="text" id="zip_code" name="zip_code" class="form-control" placeholder="{{__('messages.zip_code')}}" value="{{old('zip_code')}}">
                                <label for="zip_code">
                                    {{__('messages.zip_code')}}
                                </label>
                                @if ($errors->has('zip_code'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('zip_code') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label-group">
                                <textarea id="billing_address" name="billing_address" class="form-control" placeholder="{{__('messages.billing_address')}}" >{{old('billing_address')}}</textarea>
                                
                                @if ($errors->has('billing_address'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('billing_address') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label-group">
                                <textarea id="shipping_address" name="shipping_address" class="form-control" placeholder="{{__('messages.shipping_address')}}">{{old('shipping_address')}}</textarea>
                                
                                @if ($errors->has('shipping_address'))
                                    <span class="help-block text-danger">
                                        <small class="help-text">
                                            {{ $errors->first('shipping_address') }}
                                        </small>
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <h5 class="card-title text-center">@lang('messages.primary_contact')</h5>
                        <div class="row mx-0">

                            <div class="col-md-3">
                                <div class="form-label-group">
                                    <input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="{{__('messages.contact_name')}}" value="{{old('contact_name')}}" required>
                                    <label for="contact_name">
                                        {{__('messages.contact_name')}}
                                    </label>
                                    @if ($errors->has('contact_name'))
                                        <span class="help-block text-danger">
                                            <small class="help-text">
                                                {{ $errors->first('contact_name') }}
                                            </small>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-label-group">
                                    <input type="email" id="contact_email" name="contact_email" class="form-control" placeholder="{{__('messages.email')}}" value="{{old('contact_email')}}" required>
                                    <label for="contact_email">
                                        {{__('messages.email')}}
                                    </label>
                                    @if ($errors->has('contact_email'))
                                        <span class="help-block text-danger">
                                            <small class="help-text">
                                                {{ $errors->first('contact_email') }}
                                            </small>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-label-group">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="{{__('messages.password')}}" value="{{old('password')}}" required>
                                    <label for="password">
                                        {{__('messages.password')}}
                                    </label>
                                    @if ($errors->has('password'))
                                        <span class="help-block text-danger">
                                            <small class="help-text">
                                                {{ $errors->first('password') }}
                                            </small>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-label-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="{{__('messages.confirm_password')}}" value="" required>
                                    <label for="password_confirmation">
                                        {{__('messages.confirm_password')}}
                                    </label>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mx-auto">
                                <button class="btn btn-lg btn-primary btn-block 
                                    text-uppercase" id="submit" type="submit">
                                    {{__('messages.register')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection