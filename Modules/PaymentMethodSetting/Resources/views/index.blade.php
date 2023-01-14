@extends('backend.master')

@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.Payment Method Settings')}}</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">{{__('common.Dashboard')}} </a>
                    <a href="#">{{__('setting.Setting')}}</a>
                    <a href="#">{{__('setting.Payment Method Settings')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-40 student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-md-3 ">

                    <div class="row row pt-20 justify-content-center">
                        <div class="col-12">
                            <div class="box_header common_table_header">
                                <div class="main-title d-md-flex">
                                    <h3 class="mt-10">{{__('setting.Gateway Status')}}</h3>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="white-box ">
                        <form method="POST" action="{{route('paymentmethodsetting.changePaymentGatewayStatus')}}"
                              accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            @csrf


                            <div class="row">

                                @foreach ($payment_methods as $Key=>$payment_method)
@php
if (!paymentGateWayCredentialsEmptyCheck($payment_method->method)){
continue;
}
@endphp

                                    @if($payment_method->method=="Instamojo")
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <div class="input-effect">
                                                    <input type="checkbox" id="method_{{$payment_method->id}}"
                                                           class="common-checkbox class-checkbox read-only-input"
                                                           name="gateways[]"
                                                           value="{{$payment_method->id}}" {{$payment_method->active_status==1?'checked':''}}>
                                                    <label
                                                        for="method_{{$payment_method->id}}">{{$payment_method->method}}
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    @else
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <div class="input-effect">
                                                    <input type="checkbox" id="method_{{$payment_method->id}}"
                                                           class="common-checkbox class-checkbox read-only-input"
                                                           name="gateways[]"
                                                           value="{{$payment_method->id}}" {{$payment_method->active_status==1?'checked':''}}>
                                                    <label
                                                        for="method_{{$payment_method->id}}">{{$payment_method->method}}
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                <div class="col-lg-12 text-center">
                                    <div class="d-flex justify-content-center pt_20">
                                        <button type="submit" class="primary-btn semi_large fix-gr-bg"
                                                id="save_button_parent">
                                            <i class="ti-check"></i>
                                            {{ __('common.Update') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
                <div class="col-lg-9">
                    <div class="row pt-20">
                        <div class="main-title pt-10">
                            <h3 class="mb-25">{{__('setting.gateway_setting')}}

                                <a href="{{route('paymentmethodsetting.test')}}" class="primary-btn small fix-gr-bg   float-right" style="    position: absolute;right: 15px;">
                                    <span class="ti-hand-drag pr-2"></span>
                                    {{__('common.Test')}}
                                </a>
                            </h3>

                        </div>
                        <ul class="nav nav-tabs no-bottom-border mt-sm-md-20 mb-25 ml-3" role="tablist">
                            @foreach ($payment_methods->where('method','!=','Offline Payment')->where('method','!=','Wallet')  as $Key=>$payment_method)
                                @if($payment_method->method=="Instamojo")

                                    <li class="nav-item m-1">
                                        <a class="nav-link  {{$Key==0?'active':''}} "
                                           href="#method{{$payment_method->id}}"
                                           role="tab" data-toggle="tab">{{@$payment_method->method}}</a>
                                    </li>

                                @else
                                    <li class="nav-item m-1">
                                        <a class="nav-link  {{$Key==0?'active':''}} "
                                           href="#method{{$payment_method->id}}"
                                           role="tab" data-toggle="tab">{{@$payment_method->method}}</a>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>

                    <!-- Tab panes -->
                    <div class="tab-content">

                        @foreach ($payment_methods->where('method','!=','Offline Payment')->where('method','!=','Wallet') as $key=>$payment_method)

                            <div role="tabpanel" class="tab-pane fade  {{$key==0?'active':''}}  show "
                                 id="method{{$payment_method->id}}">

                                @if (permissionCheck('paymentmethodsetting.payment_method_setting_update'))
                                    <form class="form-horizontal"
                                          action="{{route('paymentmethodsetting.update_payment_gateway')}}"
                                          method="POST" enctype="multipart/form-data">
                                        @endif
                                        @csrf
                                        <div class="white-box">


                                            <div class="col-md-12 ">
                                                <input type="hidden" name="payment_method_id"
                                                       value="{{@$payment_method->id}}">

                                                @if($payment_method->method=="Instamojo")
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">

                                                                        <input
                                                                            class="primary-input form-control{{ $errors->has('instamojo_api_auth') ? ' is-invalid' : '' }}"
                                                                            type="text" name="instamojo_api_auth"
                                                                            id="instamojo_api_auth"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('Instamojo_API_AUTH')? getPaymentEnv('Instamojo_API_AUTH') : ''}}">
                                                                        <label>{{__('setting.API KEY')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control{{ $errors->has('instamojo_auth_token') ? ' is-invalid' : '' }}"
                                                                            type="text" name="instamojo_auth_token"
                                                                            id="instamojo_auth_token"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('Instamojo_API_AUTH_TOKEN')? getPaymentEnv('Instamojo_API_AUTH_TOKEN') : ''}}">
                                                                        <label>{{__('setting.API Auth Token')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control{{ $errors->has('instamojo_url') ? ' is-invalid' : '' }}"
                                                                            type="text" name="instamojo_url"
                                                                            id="instamojo_url"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('Instamojo_URL')? getPaymentEnv('Instamojo_URL') : ''}}">
                                                                        <label>{{__('setting.Instamojo URL')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                            </div>

                                                        </div>

                                                    </div>
                                                @endif

                                                @if($payment_method->method=="Midtrans")
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="midtrans_server_key"
                                                                            id="midtrans_server_key"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('MIDTRANS_SERVER_KEY')? getPaymentEnv('MIDTRANS_SERVER_KEY') : ''}}">
                                                                        <label>{{__('setting.Server Key')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="midtrans_env_local{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="midtrans_env"
                                                                                           @if(!getPaymentEnv('MIDTRANS_ENV'))
                                                                                           checked
                                                                                           @endif
                                                                                           id="midtrans_env_local{{$payment_method->id}}"
                                                                                           value="false"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="midtrans_env_live{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="midtrans_env"
                                                                                           id="midtrans_env_live{{$payment_method->id}}"
                                                                                           @if(getPaymentEnv('MIDTRANS_ENV'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="true"
                                                                                           class="common-radio relationButton read-only-input">

                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="input-effect">
                                                                            <div class="text-left float-left">

                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="midtrans_sanitize_no{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="midtrans_sanitiz"
                                                                                           @if(!getPaymentEnv('MIDTRANS_SANITIZE'))
                                                                                           checked
                                                                                           @endif
                                                                                           id="midtrans_sanitize_no{{$payment_method->id}}"
                                                                                           value="false"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sanitize No')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="input-effect">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="midtrans_sanitize_yes{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="midtrans_sanitiz"
                                                                                           id="midtrans_sanitize_yes{{$payment_method->id}}"
                                                                                           @if(getPaymentEnv('MIDTRANS_SANITIZE'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="true"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sanitize Yes')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="input-effect">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="midtrans_3ds_no{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="midtrans_3ds"
                                                                                           @if(!getPaymentEnv('MIDTRANS_3DS'))
                                                                                           checked
                                                                                           @endif
                                                                                           id="midtrans_3ds_no{{$payment_method->id}}"
                                                                                           value="false"
                                                                                           class="common-radio relationButton read-only-input">

                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.3DS No')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="input-effect">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="midtrans_3ds_yes{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="midtrans_3ds"
                                                                                           id="midtrans_3ds_yes{{$payment_method->id}}"
                                                                                           @if(getPaymentEnv('MIDTRANS_3DS'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="true"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.3DS Yes')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <small>
                                                                {{__('quiz.Note')}}
                                                                : {{__('setting.Make sure you have')}}
                                                                <b>{{route('midtransPaymentSuccess')}}</b> |
                                                                <b>{{route('midtransPaymentPending')}}</b> |
                                                                <b>{{route('midtransPaymentfailed')}}</b> |

                                                                {{__('setting.Set Redirection Settings In Midtrans')}}
                                                                <a
                                                                    href="https://dashboard.sandbox.midtrans.com/settings/snap_preference ">{{__('dashboard.Dashboard')}}</a>


                                                            </small>
                                                        </div>

                                                    </div>
                                                @endif
                                                @if($payment_method->method=="Payeer")
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="payeer_marchant"
                                                                            id="payeer_marchant"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYEER_MERCHANT')? getPaymentEnv('PAYEER_MERCHANT') : ''}}">
                                                                        <label>{{__('setting.Payeer Marchant')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="payeer_key"
                                                                            id="payeer_key"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYEER_KEY')? getPaymentEnv('PAYEER_KEY') : ''}}">
                                                                        <label>{{__('setting.Payeer Key')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                        <small>
                                                            {{__('quiz.Note')}}
                                                            : {{__('setting.Make sure you have')}}
                                                            <b>{{route('payeerPaymentSuccess')}}</b> |
                                                            <b>{{route('payeerPaymentfailed')}}</b>
                                                            {{__('setting.Set Redirection Settings In Payeer')}}
                                                            <a
                                                                href="https://payeer.com/en/account/api/">{{__('dashboard.Dashboard')}}</a>


                                                        </small>
                                                    </div>
                                                @endif

                                                @if($payment_method->method=="Sslcommerz")

                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">

                                                                        <input
                                                                            class="primary-input form-control{{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                                                            type="text" name="ssl_store_id"
                                                                            id="ssl_store_id"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('STORE_ID')? getPaymentEnv('STORE_ID') : ''}}">
                                                                        <label>{{__('setting.Store ID')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control{{ $errors->has('store_password') ? ' is-invalid' : '' }}"
                                                                            type="text" name="ssl_store_password"
                                                                            id="ssl_store_password"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('STORE_PASSWORD')? getPaymentEnv('STORE_PASSWORD') : ''}}">
                                                                        <label>{{__('setting.Store Password')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="input-effect">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio" name="ssl_mode"
                                                                                           @if(getPaymentEnv('IS_LOCALHOST'))
                                                                                           checked
                                                                                           @endif


                                                                                           id="mode_check_{{$payment_method->id}}"
                                                                                           value="1"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="live_mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio" name="ssl_mode"
                                                                                           id="live_mode_check_{{$payment_method->id}}"
                                                                                           @if(!getPaymentEnv('IS_LOCALHOST'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="2"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                @elseif($payment_method->method=='Pesapal')
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="pesapal_client_id"
                                                                            id="pesapal_client_id"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('PESAPAL_KEY')? getPaymentEnv('PESAPAL_KEY') : ''}}">
                                                                        <label>{{__('setting.Client ID')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control{{ $errors->has('pesapal_client_secret') ? ' is-invalid' : '' }}"
                                                                            type="text" name="pesapal_client_secret"
                                                                            id="pesapal_client_secret"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('PESAPAL_SECRET')? getPaymentEnv('PESAPAL_SECRET') : ''}}">
                                                                        <label>{{__('setting.Client Secret')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label class="primary_checkbox d-flex mr-12
"
                                                                                       for="mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="pesapal_mode"
                                                                                           @if(!getPaymentEnv('PESAPAL_IS_LIVE'))
                                                                                           checked
                                                                                           @endif


                                                                                           id="mode_check_{{$payment_method->id}}"
                                                                                           value="1"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="live_mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="pesapal_mode"
                                                                                           id="live_mode_check_{{$payment_method->id}}"
                                                                                           @if(getPaymentEnv('PESAPAL_IS_LIVE'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="2"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>

                                                @elseif($payment_method->method=='Mobilpay')
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="mobilpay_merchant_id"
                                                                            id="mobilpay_merchant_id"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('MOBILPAY_MERCHANT_ID')? getPaymentEnv('MOBILPAY_MERCHANT_ID') : ''}}">
                                                                        <label>{{__('setting.Merchant ID')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="mode_mobilpay_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="mobilpay_mode"
                                                                                           @if(getPaymentEnv('MOBILPAY_TEST_MODE'))
                                                                                           checked
                                                                                           @endif


                                                                                           id="mode_mobilpay_check_{{$payment_method->id}}"
                                                                                           value="1"
                                                                                           class="common-radio relationButton read-only-input">

                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="live_mobilpay_mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="mobilpay_mode"
                                                                                           id="live_mobilpay_mode_check_{{$payment_method->id}}"
                                                                                           @if(!getPaymentEnv('MOBILPAY_TEST_MODE'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="2"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="primary_input mb-25">
                                                                        <label class="primary_input_label"
                                                                               for="">{{__('setting.Public Key')}}</label>
                                                                        <div class="primary_file_uploader">
                                                                            <input class="primary-input filePlaceholder"
                                                                                   type="text"
                                                                                   value="{{getPaymentEnv('MOBILPAY_PUBLIC_KEY_PATH')}}"
                                                                                   placeholder="Browse public key file"
                                                                                   readonly="">
                                                                            <button class="" type="button">
                                                                                <label
                                                                                    class="primary-btn small fix-gr-bg"
                                                                                    for="document_file_public_key_{{@$payment_method->id}}">{{__('common.Browse')}}</label>
                                                                                <input type="file"
                                                                                       class="d-none fileUpload"
                                                                                       name="public_key"
                                                                                       id="document_file_public_key_{{@$payment_method->id}}"
                                                                                >
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6">
                                                                    <div class="primary_input mb-25">
                                                                        <label class="primary_input_label"
                                                                               for="">{{__('setting.Private Key')}}</label>
                                                                        <div class="primary_file_uploader">
                                                                            <input class="primary-input filePlaceholder"
                                                                                   type="text"
                                                                                   value="{{getPaymentEnv('MOBILPAY_PRIVATE_KEY_PATH')}}"
                                                                                   placeholder="Browse Private key file"
                                                                                   readonly="">
                                                                            <button class="" type="button">
                                                                                <label
                                                                                    class="primary-btn small fix-gr-bg"
                                                                                    for="document_file_private_key_{{@$payment_method->id}}">{{__('common.Browse')}}</label>
                                                                                <input type="file"
                                                                                       class="d-none fileUpload"
                                                                                       name="private_key"
                                                                                       id="document_file_private_key_{{@$payment_method->id}}"
                                                                                >
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                @elseif($payment_method->method=='PayPal')
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="paypal_client_id"
                                                                            id="paypal_client_id"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYPAL_CLIENT_ID')? getPaymentEnv('PAYPAL_CLIENT_ID') : ''}}">
                                                                        <label>{{__('setting.Client ID')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control{{ $errors->has('client_secret') ? ' is-invalid' : '' }}"
                                                                            type="text" name="paypal_client_secret"
                                                                            id="paypal_client_secret"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYPAL_CLIENT_SECRET')? getPaymentEnv('PAYPAL_CLIENT_SECRET') : ''}}">
                                                                        <label>{{__('setting.Client Secret')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="paypal_mode"
                                                                                           @if(getPaymentEnv('IS_PAYPAL_LOCALHOST'))
                                                                                           checked
                                                                                           @endif


                                                                                           id="mode_check_{{$payment_method->id}}"
                                                                                           value="1"
                                                                                           class="common-radio relationButton read-only-input">

                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">

                                                                                <label class="primary_checkbox d-flex mr-12
"
                                                                                       for="live_mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="paypal_mode"
                                                                                           id="live_mode_check_{{$payment_method->id}}"
                                                                                           @if(!getPaymentEnv('IS_PAYPAL_LOCALHOST'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="2"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>
                                                @elseif($payment_method->method=='Stripe')

                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="client_secret"
                                                                            id="secret_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('STRIPE_SECRET')? getPaymentEnv('STRIPE_SECRET') : ''}}">
                                                                        <label>{{__('setting.Secret Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="client_publisher_key"
                                                                            id="publisher_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('STRIPE_KEY')? getPaymentEnv('STRIPE_KEY') : ''}}">
                                                                        <label>{{__('setting.Publisher Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                            </div>


                                                        </div>
                                                    </div>
                                                @elseif($payment_method->method=='PayStack')

                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="paystack_key"
                                                                            id="razor_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYSTACK_PUBLIC_KEY')? getPaymentEnv('PAYSTACK_PUBLIC_KEY') : ''}}">
                                                                        <label>{{__('setting.PayStack Public Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="paystack_secret"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYSTACK_SECRET_KEY')? getPaymentEnv('PAYSTACK_SECRET_KEY') : ''}}">
                                                                        <label>{{__('setting.PayStack Secret Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="merchant_email"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('MERCHANT_EMAIL')? getPaymentEnv('MERCHANT_EMAIL') : ''}}">
                                                                        <label>{{__('setting.Merchant Email')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="paystack_payment_url"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYSTACK_PAYMENT_URL')? getPaymentEnv('PAYSTACK_PAYMENT_URL') : ''}}">
                                                                        <label>{{__('setting.PayStack Payment URL')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 mb_30">
                                                                    @if(Route::has('payStackCallBack'))
                                                                        <small>
                                                                            {{__('quiz.Note')}}
                                                                            : {{__('setting.Make sure you have')}}

                                                                            <b>{{route('payStackCallBack')}}</b>
                                                                            {{__('setting.registered in PayStack')}}
                                                                            <a
                                                                                href="https://dashboard.paystack.co/#/settings/developer ">{{__('dashboard.Dashboard')}}</a>


                                                                        </small>

                                                                    @endif
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>

                                                @elseif($payment_method->method=='RazorPay')

                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="razor_key"
                                                                            id="razor_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('RAZOR_KEY')? getPaymentEnv('RAZOR_KEY') : ''}}">
                                                                        <label>{{__('setting.Razor Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="razor_secret"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('RAZOR_SECRET')? getPaymentEnv('RAZOR_SECRET') : ''}}">
                                                                        <label>{{__('setting.Razor Secret')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                            </div>


                                                        </div>
                                                    </div>

                                                @elseif($payment_method->method=='MercadoPago')

                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="public_key"
                                                                            id="public_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('MERCADO_PUBLIC_KEY')? getPaymentEnv('MERCADO_PUBLIC_KEY') : ''}}">
                                                                        <label>{{__('setting.Public Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="access_token"
                                                                            id="access_token" autocomplete="off"
                                                                            value="{{getPaymentEnv('MERCADO_ACCESS_TOKEN')? getPaymentEnv('MERCADO_ACCESS_TOKEN') : ''}}">
                                                                        <label>{{__('setting.Access Token')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                            </div>


                                                        </div>
                                                    </div>

                                                @elseif($payment_method->method=='PayTM')

                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">

                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="paytm_merchant_id"
                                                                            id="paytm_merchant_id" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYTM_MERCHANT_ID')? getPaymentEnv('PAYTM_MERCHANT_ID') : ''}}">
                                                                        <label>{{__('setting.Merchant ID')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">

                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="paytm_mode"
                                                                                           @if(getPaymentEnv('PAYTM_ENVIRONMENT')=="local")
                                                                                           checked
                                                                                           @endif

                                                                                           id="mode_check_{{$payment_method->id}}"
                                                                                           value="local"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="live_mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="paytm_mode"
                                                                                           id="live_mode_check_{{$payment_method->id}}"
                                                                                           @if(getPaymentEnv('PAYTM_ENVIRONMENT')=="production")
                                                                                           checked
                                                                                           @endif
                                                                                           value="production"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="paytm_merchant_key"
                                                                            id="razor_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYTM_MERCHANT_KEY')? getPaymentEnv('PAYTM_MERCHANT_KEY') : ''}}">
                                                                        <label>{{__('setting.Merchant Key')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text"
                                                                            name="paytm_merchant_website"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYTM_MERCHANT_WEBSITE')? getPaymentEnv('PAYTM_MERCHANT_WEBSITE') : ''}}">
                                                                        <label>{{__('setting.PayTM Merchant Website')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="paytm_channel"
                                                                            id="razor_key" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYTM_CHANNEL')? getPaymentEnv('PAYTM_CHANNEL') : ''}}">
                                                                        <label>{{__('setting.PayTM Channel')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="industry_type"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('PAYTM_INDUSTRY_TYPE')? getPaymentEnv('PAYTM_INDUSTRY_TYPE') : ''}}">
                                                                        <label>{{__('setting.Industry Type')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                            </div>


                                                        </div>
                                                    </div>
                                                @elseif($payment_method->method=='Bkash')
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="bkash_app_key"
                                                                            id="bkash_app_key"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('BKASH_APP_KEY')? getPaymentEnv('BKASH_APP_KEY') : ''}}">
                                                                        <label>{{__('setting.App Key')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control{{ $errors->has('bkash_app_secret') ? ' is-invalid' : '' }}"
                                                                            type="text" name="bkash_app_secret"
                                                                            id="bkash_app_secret"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('BKASH_APP_SECRET')? getPaymentEnv('BKASH_APP_SECRET') : ''}}">
                                                                        <label>{{__('setting.App Secret')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            class="primary-input form-control"
                                                                            type="text" name="bkash_username"
                                                                            id="bkash_username"
                                                                            required
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('BKASH_USERNAME')? getPaymentEnv('BKASH_USERNAME') : ''}}">
                                                                        <label>{{__('setting.Username')}}
                                                                            <span></span>
                                                                        </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="bkash_password"
                                                                            id="bkash_password"
                                                                            autocomplete="off"
                                                                            value="{{getPaymentEnv('BKASH_PASSWORD')? getPaymentEnv('BKASH_PASSWORD') : ''}}">
                                                                        <label>{{__('setting.Password')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="bkash_mode"
                                                                                           @if(getPaymentEnv('IS_BKASH_LOCALHOST'))
                                                                                           checked
                                                                                           @endif


                                                                                           id="mode_check_{{$payment_method->id}}"
                                                                                           value="1"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Sandbox')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mb-25">
                                                                    <div class="input-effect">
                                                                        <div class="">
                                                                            <div class="text-left float-left">
                                                                                <label
                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                    for="live_mode_check_{{$payment_method->id}}">
                                                                                    <input type="radio"
                                                                                           name="bkash_mode"
                                                                                           id="live_mode_check_{{$payment_method->id}}"
                                                                                           @if(!getPaymentEnv('IS_BKASH_LOCALHOST'))
                                                                                           checked
                                                                                           @endif
                                                                                           value="2"
                                                                                           class="common-radio relationButton read-only-input">
                                                                                    <span
                                                                                        class="checkmark mr-2"></span> {{__('common.Live')}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>

                                                @elseif($payment_method->method=='Bank Payment')
                                                    <div class="row mb-25">
                                                        <div class="col-md-12">
                                                            <div class="row">


                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="bank_name"
                                                                            id="bank_name" autocomplete="off"
                                                                            value="{{getPaymentEnv('BANK_NAME')? getPaymentEnv('BANK_NAME') : ''}}">
                                                                        <label>{{__('setting.Bank Name')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-4 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="branch_name"
                                                                            id="branch_name" autocomplete="off"
                                                                            value="{{getPaymentEnv('BRANCH_NAME')? getPaymentEnv('BRANCH_NAME') : ''}}">
                                                                        <label>{{__('setting.Branch Name')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-4 mb-25" style="margin-top: -10px;">
                                                                    <div class="input-effect">

                                                                        <select class="primary_select" name="type"
                                                                                id="type"
                                                                                style="margin-top: -10px;">
                                                                            <option
                                                                                data-display="{{__('common.Select')}}  {{__('setting.Account Type')}}"
                                                                                value="">{{__('common.Select')}} {{__('setting.Account Type')}}</option>
                                                                            <option
                                                                                value="Current Account" {{(getPaymentEnv('ACCOUNT_TYPE')? getPaymentEnv('ACCOUNT_TYPE') : '')=='Current Account'?'selected':''}}>
                                                                                Current Account
                                                                            </option>

                                                                            <option
                                                                                value="Savings Account" {{(getPaymentEnv('ACCOUNT_TYPE')? getPaymentEnv('ACCOUNT_TYPE') : '')=='Savings Account'?'selected':''}}>
                                                                                Savings Account
                                                                            </option>
                                                                            <option
                                                                                value="Salary Account" {{(getPaymentEnv('ACCOUNT_TYPE')? getPaymentEnv('ACCOUNT_TYPE') : '')=='Salary Account'?'selected':''}}>
                                                                                Salary Account
                                                                            </option>
                                                                            <option
                                                                                value="Fixed Deposit" {{(getPaymentEnv('ACCOUNT_TYPE')? getPaymentEnv('ACCOUNT_TYPE') : '')=='Fixed Deposit'?'selected':''}}>
                                                                                Fixed Deposit
                                                                            </option>

                                                                        </select>


                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control "
                                                                            type="text" name="account_number"
                                                                            id="account_number" autocomplete="off"
                                                                            value="{{getPaymentEnv('ACCOUNT_NUMBER')? getPaymentEnv('ACCOUNT_NUMBER') : ''}}">
                                                                        <label>{{__('setting.Account Number')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mb-25">
                                                                    <div class="input-effect">
                                                                        <input
                                                                            required
                                                                            class="primary-input form-control"
                                                                            type="text" name="account_holder"
                                                                            id="razor_secret" autocomplete="off"
                                                                            value="{{getPaymentEnv('ACCOUNT_HOLDER')? getPaymentEnv('ACCOUNT_HOLDER') : ''}}">
                                                                        <label>{{__('setting.Account Holder')}}
                                                                            <span></span> </label>
                                                                        <span class="focus-border"></span>
                                                                        <span
                                                                            class="modal_input_validation red_alert"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                @endif

                                                @if($payment_method->method!="Bank Payment" && $payment_method->method!="Offline Payment" && $payment_method->method!="Wallet")

                                                    <div class="row imageBox">
                                                        <div class="col-xl-6">
                                                            <div class="primary_input mb-35">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Logo')}}</label>
                                                                <div class="primary_file_uploader">
                                                                    <input class="primary-input filePlaceholder"
                                                                           type="text"
                                                                           value="{{showPicName(@$payment_method->logo)}}"
                                                                           placeholder="Browse Image file"
                                                                           readonly="">
                                                                    <button class="" type="button">
                                                                        <label
                                                                            class="primary-btn small fix-gr-bg"
                                                                            for="document_file_1_edit_{{@$payment_method->id}}">{{__('common.Browse')}}</label>
                                                                        <input type="file"
                                                                               class="d-none fileUpload"
                                                                               name="logo"
                                                                               id="document_file_1_edit_{{@$payment_method->id}}"
                                                                        >
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <img class="  p-3 preview"
                                                                 style="max-height: 100px;max-width: 100%"
                                                                 src="{{asset($payment_method->logo)}}"
                                                                 alt="">
                                                        </div>
                                                    </div>
                                                @endif


                                                <div class="row mt-40">
                                                    <div class="col-lg-12 text-center">
                                                        <button class="primary-btn fix-gr-bg">
                                                            <span class="ti-check"></span>
                                                            {{__('common.Update')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                            </div>
                        @endforeach

                    </div>
                </div>


            </div>
        </div>
    </section>

@endsection
@push('scripts')
    <script src="{{asset('public/backend/js/gateway.js')}}"></script>
@endpush
