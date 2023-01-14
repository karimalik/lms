@extends('backend.master')
@section('mainContent')
    <style>
        .white-box.single-summery {
            padding: 21px 0px;
        }

        .white-box.single-summery h1 {
            font-size: 20px;
        }
    </style>

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.Utilities')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('setting.Settings')}}</a>
                    <a href="#">{{__('setting.Utilities')}}</a>
                </div>
            </div>
        </div>
    </section>

    <div class="row justify-content-center">

        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4 col-lg-3 col-sm-6">
                    <a class="white-box single-summery d-block btn-ajax"
                       href="{{ route('setting.utilities', ['utilities' => 'optimize_clear']) }}">
                        <div class="d-block mt-10 text-center ">
                            <h3><i class="ti-cloud font_30"></i></h3>
                            <h1 class="gradient-color2 total_purchase">{{ __('setting.Clear Cache') }}</h1>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-lg-3 col-sm-6">
                    <a class="white-box single-summery d-block btn-ajax"
                       href="{{ route('setting.utilities', ['utilities' => 'clear_log']) }}">
                        <div class="d-block mt-10 text-center ">
                            <h3><i class="ti-receipt font_30"></i></h3>
                            <h1 class="gradient-color2 total_purchase">{{ __('setting.Clear Log') }}</h1>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-lg-3 col-sm-6">
                    <a class="white-box single-summery d-block btn-ajax"
                       href="{{ route('setting.utilities', ['utilities' => 'change_debug']) }}">
                        <div class="d-block mt-10 text-center ">
                            <h3><i class="ti-blackboard font_30"></i></h3>
                            <h1 class="gradient-color2 total_purchase"> {{ __((env('APP_DEBUG') ? "Disable" : "Enable" ).' App Debug') }}</h1>
                        </div>
                    </a>
                </div>


                <div class="col-md-4 col-lg-3 col-sm-6">
                    <a class="white-box single-summery d-block btn-ajax"
                       href="{{ route('setting.utilities', ['utilities' => 'force_https']) }}">
                        <div class="d-block mt-10 text-center ">
                            <h3><i class="ti-lock font_30"></i></h3>
                            <h1 class="gradient-color2 total_purchase"> {{ __((env('FORCE_HTTPS') ? "Disable" : "Enable" ).' Force HTTPS') }}</h1>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-lg-3 col-sm-6">
                    <a class="white-box single-summery d-block btn-ajax" id="import_database_card" href="#"
                       data-toggle="modal" data-target="#ImportDatabaseModal">
                        <div class="d-block mt-10 text-center ">
                            <h3><i class="fas fa-database font_30"></i></h3>
                            <h1 class="gradient-color2 total_purchase"> {{ __('setting.Import Demo Database') }}</h1>
                        </div>
                    </a>
                </div>


                <div class="col-md-4 col-lg-3 col-sm-6">
                    <a class="white-box single-summery d-block btn-ajax" id="reset_database_card" href="#"
                       data-toggle="modal" data-target="#resetModal"
                    >
                        <div class="d-block mt-10 text-center ">
                            <h3><i class="fas fa-database font_30"></i></h3>
                            <h1 class="gradient-color2 total_purchase"> {{ __('setting.Reset Database') }}</h1>
                        </div>
                    </a>
                </div>
                <div class="col-lg-12">
                    <div class="alert alert-warning mt-30 text-center">
                        {{__('setting.It can take some times to execute operation. please wait until completed operation')}}
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Reset Modal --}}
    <div class="modal fade admin-query" id="resetModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('setting.Reset Database')</h4>
                    <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <strong>{{__('setting.reset_database_note')}}</strong>
                        <h4>@lang('setting.Are you sure to reset database')?</h4>
                    </div>
                    <div class="mt-40 justify-content-between">
                        <form id="activate_form" action="{{route('utilities.resetDatabase')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label"
                                               for="title">{{__('common.Enter Password')}} <span
                                                class="text-danger">*</span></label>
                                        <input required type="password" id="password"
                                               class="primary_input_field" name="password" autocomplete="off"
                                               value="" placeholder="{{__('common.Enter Password')}} ">

                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg"
                                            id="save_button_parent">{{ __('setting.Reset Database') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- import database modal --}}
    <div class="modal fade admin-query" id="ImportDatabaseModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('setting.import_demo_database')</h4>
                    <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <strong>{{__('setting.import_demo_note')}}</strong>
                        <h4>@lang('setting.are_you_sure_to_import_demo_database')</h4>
                    </div>

                    <div class="mt-40 justify-content-between">
                        <form id="activate_form" action="{{route('utilities.importDemoDatabase')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label"
                                               for="title">{{__('common.Enter Password')}} <span
                                                class="text-danger">*</span></label>
                                        <input required type="password" id="password"
                                               class="primary_input_field" name="password" autocomplete="off"
                                               value="" placeholder="{{__('common.Enter Password')}} ">

                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg"
                                            id="save_button_parent">{{ __('setting.Import Database') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection




