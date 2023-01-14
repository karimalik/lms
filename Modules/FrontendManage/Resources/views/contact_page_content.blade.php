@extends('backend.master')
@section('table'){{__('testimonials')}}@endsection
@section('mainContent')
    @include("backend.partials.alertMessage")
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('frontendmanage.Page Content')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('frontendmanage.Frontend CMS')}}</a>
                    <a class="active" href="{{url('frontend/page-content')}}">{{__('frontendmanage.Page Content')}}</a>
                </div>
            </div>
        </div>
    </section>


    <section class="mb-40 student-details">
        <div class="container-fluid p-0">
            <div class="row">

                <div class="col-lg-12">


                    @if (permissionCheck('null'))
                        <form class="form-horizontal" action="{{route('frontend.ContactPageContentUpdate')}}"
                              method="POST"
                              enctype="multipart/form-data">
                            @endif
                            @csrf
                            <div class="white-box">

                                <div class="col-md-12 ">
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    <input type="hidden" name="id" value="{{@$page_content->id}}">
                                    <div class="row mb-30">
                                        <div class="col-md-12">

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Contact Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Contact Page Title') }}"
                                                               type="text" name="contact_page_title"
                                                               {{ $errors->has('contact_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->contact_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Contact Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Contact Page Sub Title') }}"
                                                               type="text" name="contact_sub_title"
                                                               {{ $errors->has('contact_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->contact_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview5"
                                                             src="{{ asset('/'.$page_content->contact_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Contact Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                class="primary-input  filePlaceholder {{ @$errors->has('contact_page_banner') ? ' is-invalid' : '' }}"
                                                                type="text" id=""
                                                                placeholder="Browse file"
                                                                readonly="" {{ $errors->has('contact_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file5">{{ __('common.Browse') }}</label>
                                                                <input type="file" class="d-none fileUpload imgInput5"
                                                                       name="contact_page_banner" id="file5">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">

                                                    <div class="mt-5">
                                                        <label class="switch_toggle "
                                                               for="show_map">
                                                            <input type="checkbox" class="status_enable_disable"
                                                                   name="show_map"
                                                                   id="show_map"
                                                                   @if (@$page_content->show_map == 1) checked
                                                                   @endif value="1">
                                                            <i class="slider round"></i>


                                                        </label>
                                                        {{__('frontendmanage.Show Map')}}

                                                    </div>
                                                </div>

                                                @if(currentTheme()=="edume")
                                                    <div class="col-xl-2">
                                                        <div class="primary_input mb-25">
                                                            <img height="70" class="w-100 imagePreview6"
                                                                 src="{{ asset('/'.$page_content->contact_page_body_image)}}"
                                                                 alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Contact Page Image') }}
                                                                <small>({{__('common.Recommended Size')}}
                                                                    480x680)</small>
                                                            </label>
                                                            <div class="primary_file_uploader">
                                                                <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('contact_page_body_image') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('contact_page_body_image') ? ' autofocus' : '' }}>
                                                                <button class="" type="button">
                                                                    <label class="primary-btn small fix-gr-bg"
                                                                           for="file6">{{ __('common.Browse') }}</label>
                                                                    <input type="file"
                                                                           class="d-none fileUpload imgInput6"
                                                                           name="contact_page_body_image" id="file6">
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                            @if(currentTheme()=="edume")
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Contact Page Content Title') }}
                                                            </label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('frontendmanage.Contact Page Content Title') }}"
                                                                   type="text" name="contact_page_content_title"
                                                                   {{ $errors->has('contact_page_content_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->contact_page_content_title : ''}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Contact Page Content Details') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('frontendmanage.Contact Page Content Details') }}"
                                                                   type="text" name="contact_page_content_details"
                                                                   {{ $errors->has('contact_page_content_details') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->contact_page_content_details : ''}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-4">

                                                        <div class="row">
                                                            <div class="col-xl-4">
                                                                <div class="primary_input mb-25">
                                                                    <img height="70" class="w-100 imagePreview1"
                                                                         src="{{ asset('/'.$page_content->contact_page_phone)}}"
                                                                         alt="">
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-8">
                                                                <div class="primary_input mb-25">
                                                                    <label class="primary_input_label"
                                                                           for="">{{ __('frontendmanage.Phone Logo') }}
                                                                        <small>({{__('common.Recommended Size')}}
                                                                            60x60)</small>
                                                                    </label>
                                                                    <div class="primary_file_uploader">
                                                                        <input
                                                                            class="primary-input  filePlaceholder {{ @$errors->has('contact_page_phone') ? ' is-invalid' : '' }}"
                                                                            type="text" id=""
                                                                            placeholder="Browse file"
                                                                            readonly="" {{ $errors->has('contact_page_phone') ? ' autofocus' : '' }}>
                                                                        <button class="" type="button">
                                                                            <label class="primary-btn small fix-gr-bg"
                                                                                   for="file1">{{ __('common.Browse') }}</label>
                                                                            <input type="file"
                                                                                   class="d-none fileUpload imgInput1"
                                                                                   name="contact_page_phone" id="file1">
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-xl-4">

                                                        <div class="row">
                                                            <div class="col-xl-4">
                                                                <div class="primary_input mb-25">
                                                                    <img height="70" class="w-100 imagePreview2"
                                                                         src="{{ asset('/'.$page_content->contact_page_email)}}"
                                                                         alt="">
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-8">
                                                                <div class="primary_input mb-25">
                                                                    <label class="primary_input_label"
                                                                           for="">{{ __('frontendmanage.Email Logo') }}
                                                                        <small>({{__('common.Recommended Size')}}
                                                                            60x60)</small>
                                                                    </label>
                                                                    <div class="primary_file_uploader">
                                                                        <input
                                                                            class="primary-input  filePlaceholder {{ @$errors->has('contact_page_email') ? ' is-invalid' : '' }}"
                                                                            type="text" id=""
                                                                            placeholder="Browse file"
                                                                            readonly="" {{ $errors->has('contact_page_email') ? ' autofocus' : '' }}>
                                                                        <button class="" type="button">
                                                                            <label class="primary-btn small fix-gr-bg"
                                                                                   for="file2">{{ __('common.Browse') }}</label>
                                                                            <input type="file"
                                                                                   class="d-none fileUpload imgInput2"
                                                                                   name="contact_page_email" id="file2">
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-xl-4">

                                                        <div class="row">
                                                            <div class="col-xl-4">
                                                                <div class="primary_input mb-25">
                                                                    <img height="70" class="w-100 imagePreview3"
                                                                         src="{{ asset('/'.$page_content->contact_page_address)}}"
                                                                         alt="">
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-8">
                                                                <div class="primary_input mb-25">
                                                                    <label class="primary_input_label"
                                                                           for="">{{ __('frontendmanage.Address Logo') }}
                                                                        <small>({{__('common.Recommended Size')}}
                                                                            60x60)</small>
                                                                    </label>
                                                                    <div class="primary_file_uploader">
                                                                        <input
                                                                            class="primary-input  filePlaceholder {{ @$errors->has('contact_page_address') ? ' is-invalid' : '' }}"
                                                                            type="text" id=""
                                                                            placeholder="Browse file"
                                                                            readonly="" {{ $errors->has('contact_page_address') ? ' autofocus' : '' }}>
                                                                        <button class="" type="button">
                                                                            <label class="primary-btn small fix-gr-bg"
                                                                                   for="file3">{{ __('common.Browse') }}</label>
                                                                            <input type="file"
                                                                                   class="d-none fileUpload imgInput3"
                                                                                   name="contact_page_address"
                                                                                   id="file3">
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $tooltip = "";
                                    if(permissionCheck('null')){
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

        function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview2").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput2").change(function () {
            readURL2(this);
        });


        function readURL3(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview3").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput3").change(function () {
            readURL3(this);
        });


        function readURL4(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview4").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput4").change(function () {
            readURL4(this);
        });


        function readURL5(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview5").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput5").change(function () {
            readURL5(this);
        });
        $(".imgInput4").change(function () {
            readURL4(this);
        });


        function readURL6(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview6").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput6").change(function () {
            readURL6(this);
        });

        function readURL7(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview7").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput7").change(function () {
            readURL7(this);
        });

        function readURL8(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview8").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput8").change(function () {
            readURL8(this);
        });


        function readURL9(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview9").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput9").change(function () {
            readURL9(this);
        });

        function readURL10(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview10").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput10").change(function () {
            readURL10(this);
        });
    </script>
@endpush
