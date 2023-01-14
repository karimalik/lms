@extends('backend.master')

@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.Maintenance')}} {{__('setting.Setting')}}</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">{{__('common.Dashboard')}} </a>
                    <a href="#">{{__('setting.Setting')}}</a>
                    <a href="#">{{__('setting.Maintenance')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-40 student-details">
        <div class="container-fluid p-0">
            <div class="row">

                <div class="col-lg-12">
                    <div class="row pt-20">
                        <div class="main-title pl-3 pt-10">
                            <h3 class="mb-30">{{__('setting.Maintenance')}} {{__('setting.Setting')}}</h3>
                        </div>
                    </div>

                    <form class="form-horizontal" action="{{route('setting.maintenance')}}" method="POST" enctype="multipart/form-data">

                        @csrf
                        <div class="white-box">

                            <div class="col-md-12 p-0">

                                <div class="row mb-30">
                                    <div class="col-md-12">

                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('common.Title') }}</label>
                                                    <input class="primary_input_field" placeholder="-" type="text"
                                                           name="maintenance_title"
                                                           value="{{$setting->maintenance_title}}">
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('common.Sub Title') }}  </label>
                                                    <input class="primary_input_field" placeholder="-" type="text"
                                                           name="maintenance_sub_title"
                                                           value="{{$setting->maintenance_sub_title}}">
                                                </div>
                                            </div>
                                            <div class="col-xl-2">
                                                <div class="primary_input mb-25">
                                                    <img height="70" class="w-100 imagePreview1"
                                                         src="{{asset($setting->maintenance_banner)}}"
                                                         alt="">
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('frontendmanage.Maintenance Page Banner') }}
                                                    </label>
                                                    <div class="primary_file_uploader">
                                                        <input
                                                            class="primary-input  filePlaceholder {{ @$errors->has('course_page_banner') ? ' is-invalid' : '' }}"
                                                            type="text" id=""
                                                            placeholder="Browse file"
                                                            readonly="" {{ $errors->has('course_page_banner') ? ' autofocus' : '' }}>
                                                        <button class="" type="button">
                                                            <label class="primary-btn small fix-gr-bg"
                                                                   for="file1">{{ __('common.Browse') }}</label>
                                                            <input type="file" class="d-none fileUpload imgInput1"
                                                                   name="maintenance_banner" id="file1">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 dripCheck">
                                                <div class="primary_input mb-25">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="primary_input_label"
                                                                   for="    "> {{__('setting.Maintenance')}} {{__('common.Mode')}}</label>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4 mb-25">
                                                                    <label class="primary_checkbox d-flex mr-12"
                                                                        for="yes">
                                                                    <input type="radio"
                                                                           class="common-radio "
                                                                           id="yes"
                                                                           name="maintenance_status"
                                                                           {{$setting->maintenance_status==1?'checked':''}}
                                                                           value="1">
                                                                        <span class="checkmark mr-2"></span>  {{__('common.Yes')}}</label>
                                                                </div>
                                                                <div class="col-md-4 mb-25">
                                                                    <label class="primary_checkbox d-flex mr-12"
                                                                        for="no">
                                                                    <input type="radio"
                                                                           class="common-radio "
                                                                           id="no"
                                                                           name="maintenance_status"
                                                                           value="0" {{$setting->maintenance_status==0?'checked':''}}>
                                                                        <span class="checkmark mr-2"></span>   {{__('common.No')}}</label>
                                                                </div>
                                                            </div>


                                                        </div>

                                                    </div>
                                                </div>
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

                            <div class="row mt-40">
                                <div class="col-lg-12 text-center">
                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                    >
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
@push('scripts')
    <script>
        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview1").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput1").change(function () {

            readURL1(this);
        });
    </script>
@endpush
