@extends('backend.master')

@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.VdoCipher Configuration')}}</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">{{__('common.Dashboard')}} </a>
                    <a href="#">{{__('setting.Setting')}}</a>
                    <a href="#">{{__('setting.VdoCipher Configuration')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-40 student-details">
        <div class="container-fluid p-0">
            <div class="row">

                <div class="col-lg-12">

                    @if (permissionCheck('vdocipher.settingUpdate'))
                        <form class="form-horizontal" action="{{route('vdocipher.settingUpdate')}}" method="POST">
                            @endif
                            @csrf
                            <div class="white-box">

                                <div class="col-md-12 p-0">

                                    <div class="row mb-30">
                                        <div class="col-md-12">

                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('setting.API Secret') }} *</label>
                                                        <input class="primary_input_field" placeholder="-" type="text"
                                                               name="api_secret" value="{{saasEnv('VDOCIPHER_API_SECRET')}}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <code><a target="_blank"
                                                             href="https://www.vdocipher.com/dashboard/config/apikeys">{{__('setting.Click Here to Get VdoCipher Api Key')}}</a></code>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <div class="row justify-content-center">

                                                @if(session()->has('message-success'))
                                                    <p class=" text-success">
                                                        {{ session()->get('message-success') }}
                                                    </p>
                                                @elseif(session()->has('message-danger'))
                                                    <p class=" text-danger">
                                                        {{ session()->get('message-danger') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $tooltip = "";
                                    if(permissionCheck('vdocipher.settingUpdate')){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to Update";
                                    }
                                @endphp
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                                title="{{$tooltip}}">
                                            <span class="ti-check"></span>
                                            {{__('common.Update')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </section>

@endsection
