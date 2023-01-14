@extends('backend.master')
@section('table'){{__('social_links')}}@endsection
@section('mainContent')
    @include("backend.partials.alertMessage")
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.Instructor Setup')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('setting.Settings')}}</a>
                    <a class="active"
                       href="{{ route('settings.instructor_setup') }}">{{__('setting.Instructor Setup')}}</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">

                    <div class="white-box mb_30 ">
                            <form action="{{route('settings.instructor_setup_update')}}" method="post" id="coupon-form" name="coupon-form" enctype="multipart/form-data">

                                            @csrf

                                            <div class="row">
                                                <div class="col-lg-6">
                                                        <div class="mb_25">
                                                            <label class="switch_toggle "
                                                                   for="show_instructor_page_banner">
                                                                <input type="checkbox" class="status_enable_disable"
                                                                       name="show_instructor_page_banner"
                                                                       id="show_instructor_page_banner"
                                                                       @if (@$data->show_instructor_page_banner == 1) checked
                                                                       @endif value="1">
                                                                <i class="slider round"></i>


                                                            </label>
                                                            {{ __('frontendmanage.Instructor Page Banner') }}

                                                        </div>
                                                </div>

                                            </div>


                                            <div class="row">

                                                <div class="col-lg-12 text-center">
                                                    <div class="d-flex justify-content-center pt_20">
                                                        <button type="submit" class="primary-btn semi_large fix-gr-bg"
                                                                data-toggle="tooltip"
                                                                id="save_button_parent">
                                                            <i class="ti-check"></i>
                                                            @if(!isset($edit)) {{ __('common.Save') }} @else {{ __('common.Update') }} @endif
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('backend.partials.delete_modal')
@endsection
@push('scripts')

@endpush
