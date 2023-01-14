@extends('backend.master')
@section('table'){{__('privacy_policies')}}@endsection
@section('mainContent')
    @include("backend.partials.alertMessage")
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('frontendmanage.Privacy Policy')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('frontendmanage.Frontend CMS')}}</a>
                    <a class="active" href="#">{{__('frontendmanage.Privacy Policy')}}</a>
                </div>
            </div>
        </div>
    </section>


    <section class="mb-20 student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20"><a target="_blank"
                                                                                 href="{{route('privacy')}}"
                                                                                 class="primary-btn small fix-gr-bg"> <span
                            class="ti-eye pr-2"></span> {{__('student.Preview')}} </a></div>
                <div class="col-lg-12">


                    @if (permissionCheck('null'))
                        <form class="form-horizontal" action="{{route('frontend.privacy_policy_Update')}}" method="POST"
                              enctype="multipart/form-data">
                            @endif
                            @csrf
                            <div class="white-box">

                                <div class="col-md-12 ">
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    <input type="hidden" name="id" value="{{@$privacy_policy->id}}">
                                    <div class="row mb-30">
                                        <div class="col-md-12 p-0">

                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="row">
                                                        <div class="col-xl-6">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{ __('frontendmanage.Page Title') }}
                                                                </label>
                                                                <input class="primary_input_field"
                                                                       placeholder="{{ __('frontendmanage.Page Title') }}"
                                                                       type="text" name="page_banner_title"
                                                                       {{ $errors->has('course_page_title') ? ' autofocus' : '' }}
                                                                       value="{{isset($privacy_policy)? $privacy_policy->page_banner_title : ''}}">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{ __('frontendmanage.Page Sub Title') }}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder="{{ __('frontendmanage.Page Sub Title') }}"
                                                                       type="text" name="page_banner_sub_title"
                                                                       {{ $errors->has('page_sub_title') ? ' autofocus' : '' }}
                                                                       value="{{isset($privacy_policy)? $privacy_policy->page_banner_sub_title : ''}}">
                                                            </div>
                                                        </div>


                                                        <div class="col-xl-2">
                                                            <div class="primary_input mb-25">
                                                                <img height="70" class="w-100 imagePreview1"
                                                                     src="{{ asset('/'.$privacy_policy->page_banner)}}"
                                                                     alt="">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{ __('frontendmanage.Page Banner') }}
                                                                    <small>({{__('common.Recommended Size')}}
                                                                        1920x500)</small>
                                                                </label>
                                                                <div class="primary_file_uploader">
                                                                    <input
                                                                        class="primary-input  filePlaceholder {{ @$errors->has('page_banner') ? ' is-invalid' : '' }}"
                                                                        type="text" id=""
                                                                        placeholder="Browse file"
                                                                        readonly="" {{ $errors->has('page_banner') ? ' autofocus' : '' }}>
                                                                    <button class="" type="button">
                                                                        <label class="primary-btn small fix-gr-bg"
                                                                               for="file1">{{ __('common.Browse') }}</label>
                                                                        <input type="file"
                                                                               class="d-none fileUpload imgInput1"
                                                                               name="page_banner" id="file1">
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <div class="row">
                                                                <div class="col-md-12 pb-2">
                                                                    <label
                                                                        for="">{{__('frontendmanage.Page Banner Status')}}</label>
                                                                </div>
                                                                <div class="col-md-6 mb-25">
                                                                    <label class="primary_checkbox d-flex mr-12"
                                                                           for="status0">
                                                                        <input type="radio"
                                                                               class="common-radio "
                                                                               id="status0"
                                                                               name="page_banner_status"
                                                                               value="0" {{@$privacy_policy->page_banner_status==0?"checked":""}}>
                                                                        <span
                                                                            class="checkmark mr-2"></span> {{__('common.No')}}
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-6 mb-25">
                                                                    <label class="primary_checkbox d-flex mr-12"
                                                                           for="status1">
                                                                        <input type="radio"
                                                                               class="common-radio"
                                                                               id="status1"
                                                                               name="page_banner_status"
                                                                               value="1" {{@$privacy_policy->page_banner_status==1?"checked":""}}>

                                                                        <span
                                                                            class="checkmark mr-2"></span> {{__('common.Yes')}}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{__('frontendmanage.Privacy Policy')}}</label>
                                                        <textarea name="details"
                                                                  {{ $errors->has('details') ? ' autofocus' : '' }} class="lms_summernote"
                                                                  cols="30" rows="10"
                                                                  placeholder="{{__('frontendmanage.Privacy Policy')}}"
                                                                  class="textArea {{ @$errors->has('details') ? ' is-invalid' : '' }}">{{isset($privacy_policy)?$privacy_policy->details:old('details')}}</textarea>
                                                        @if ($errors->has('details'))
                                                            <span class="invalid-feedback d-block mb-10" role="alert">
                                                                <strong>{{ @$errors->first('details') }}</strong>
                                                            </span>
                                                        @endif
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
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".imgInput1").change(function () {

            readURL1(this);
        });
    </script>
@endpush
