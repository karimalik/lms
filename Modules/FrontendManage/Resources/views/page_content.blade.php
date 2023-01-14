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
                        <form class="form-horizontal" action="{{route('frontend.pageContent_Update')}}" method="POST"
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
                                                               for="">{{ __('frontendmanage.Course Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Course Page Title') }}"
                                                               type="text" name="course_page_title"
                                                               {{ $errors->has('course_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->course_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Course Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Course Page Sub Title') }}"
                                                               type="text" name="course_page_sub_title"
                                                               {{ $errors->has('course_page_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->course_page_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview1"
                                                             src="{{ asset('/'.$page_content->course_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Course Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
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
                                                                       name="course_page_banner" id="file1">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Class Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Class Page Title') }}"
                                                               type="text" name="class_page_title"
                                                               {{ $errors->has('class_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->class_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Class Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Class Page Sub Title') }}"
                                                               type="text" name="class_page_sub_title"
                                                               {{ $errors->has('class_page_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->class_page_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview2"
                                                             src="{{ asset('/'.$page_content->class_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Class Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('class_page_banner') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('class_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file2">{{ __('common.Browse') }}</label>
                                                                <input type="file" class="d-none fileUpload imgInput2"
                                                                       name="class_page_banner" id="file2">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Quiz Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Quiz Page Title') }}"
                                                               type="text" name="quiz_page_title"
                                                               {{ $errors->has('class_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->quiz_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Quiz Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Quiz Page Sub Title') }}"
                                                               type="text" name="quiz_page_sub_title"
                                                               {{ $errors->has('quiz_page_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->quiz_page_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview3"
                                                             src="{{ asset('/'.$page_content->quiz_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Quiz Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('quiz_page_banner') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('quiz_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file3">{{ __('common.Browse') }}</label>
                                                                <input type="file" class="d-none fileUpload imgInput3"
                                                                       name="quiz_page_banner" id="file3">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Instructor Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Instructor Page Title') }}"
                                                               type="text" name="instructor_page_title"
                                                               {{ $errors->has('instructor_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->instructor_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Instructor Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Instructor Page Sub Title') }}"
                                                               type="text" name="instructor_page_sub_title"
                                                               {{ $errors->has('instructor_page_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->instructor_page_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview4"
                                                             src="{{ asset('/'.$page_content->instructor_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Instructor Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('instructor_page_banner') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('instructor_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file4">{{ __('common.Browse') }}</label>
                                                                <input type="file"
                                                                       class="d-none fileUpload imgInput4   "
                                                                       name="instructor_page_banner" id="file4">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Become Instructor Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Become Instructor Page Title') }}"
                                                               type="text" name="become_instructor_page_title"
                                                               {{ $errors->has('become_instructor_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->become_instructor_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Become Instructor Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Become Instructor Page Sub Title') }}"
                                                               type="text" name="become_instructor_page_sub_title"
                                                               {{ $errors->has('become_instructor_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->become_instructor_page_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview8"
                                                             src="{{ asset('/'.$page_content->become_instructor_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Become Instructor Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('become_instructor_page_banner') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('become_instructor_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file8">{{ __('common.Browse') }}</label>
                                                                <input type="file"
                                                                       class="d-none fileUpload imgInput8   "
                                                                       name="become_instructor_page_banner" id="file8">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.About Page Title') }}
                                                        </label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.About Page Title') }}"
                                                               type="text" name="about_page_title"
                                                               {{ $errors->has('about_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->about_page_title : ''}}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.About Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.About Page Sub Title') }}"
                                                               type="text" name="about_sub_title"
                                                               {{ $errors->has('about_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->about_sub_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">
                                                        <img height="70" class="w-100 imagePreview6"
                                                             src="{{ asset('/'.$page_content->about_page_banner)}}"
                                                             alt="">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.About Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('instructor_page_banner') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('about_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file6">{{ __('common.Browse') }}</label>
                                                                <input type="file" class="d-none fileUpload imgInput6"
                                                                       name="about_page_banner" id="file6">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(isModuleActive('Subscription'))
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('subscription.Subscription Page Title') }}
                                                            </label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('subscription.Subscription Page Title') }}"
                                                                   type="text" name="subscription_page_title"
                                                                   {{ $errors->has('subscription_page_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->subscription_page_title : ''}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('subscription.Subscription Page Sub Title') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('subscription.Subscription Page Sub Title') }}"
                                                                   type="text" name="subscription_page_sub_title"
                                                                   {{ $errors->has('subscription_page_sub_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->subscription_page_sub_title : ''}}">
                                                        </div>
                                                    </div>


                                                    <div class="col-xl-2">
                                                        <div class="primary_input mb-25">
                                                            <img height="70" class="w-100 imagePreview9"
                                                                 src="{{ asset('/'.$page_content->subscription_page_banner)}}"
                                                                 alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Subscription Page Banner') }}
                                                                <small>({{__('common.Recommended Size')}}
                                                                    1920x500)</small>
                                                            </label>
                                                            <div class="primary_file_uploader">
                                                                <input
                                                                        class="primary-input  filePlaceholder {{ @$errors->has('subscription_page_banner') ? ' is-invalid' : '' }}"
                                                                        type="text" id=""
                                                                        placeholder="Browse file"
                                                                        readonly="" {{ $errors->has('subscription_page_banner') ? ' autofocus' : '' }}>
                                                                <button class="" type="button">
                                                                    <label class="primary-btn small fix-gr-bg"
                                                                           for="file9">{{ __('common.Browse') }}</label>
                                                                    <input type="file"
                                                                           class="d-none fileUpload imgInput9"
                                                                           name="subscription_page_banner" id="file9">
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(isModuleActive('Forum'))
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('forum.Forum Page Title') }}
                                                            </label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('forum.Forum Page Title') }}"
                                                                   type="text" name="forum_title"
                                                                   {{ $errors->has('forum_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->forum_title : ''}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('forum.Forum Page Sub Title') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('forum.Forum Page Sub Title') }}"
                                                                   type="text" name="forum_sub_title"
                                                                   {{ $errors->has('forum_sub_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->forum_sub_title : ''}}">
                                                        </div>
                                                    </div>


                                                    <div class="col-xl-2">
                                                        <div class="primary_input mb-25">
                                                            <img height="70" class="w-100 imagePreview9"
                                                                 src="{{ asset('/'.$page_content->forum_banner)}}"
                                                                 alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('forum.Forum Page Banner') }}
                                                                <small>({{__('common.Recommended Size')}}
                                                                    1920x500)</small>
                                                            </label>
                                                            <div class="primary_file_uploader">
                                                                <input
                                                                        class="primary-input  filePlaceholder {{ @$errors->has('forum_banner') ? ' is-invalid' : '' }}"
                                                                        type="text" id=""
                                                                        placeholder="Browse file"
                                                                        readonly="" {{ $errors->has('forum_banner') ? ' autofocus' : '' }}>
                                                                <button class="" type="button">
                                                                    <label class="primary-btn small fix-gr-bg"
                                                                           for="file9">{{ __('common.Browse') }}</label>
                                                                    <input type="file"
                                                                           class="d-none fileUpload imgInput9"
                                                                           name="forum_banner" id="file9">
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row">

                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.BLog Page Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.Blog Page Title') }}"
                                                               type="text" name="blog_page_title"
                                                               {{ $errors->has('blog_page_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->blog_page_title : ''}}">
                                                    </div>
                                                </div>


                                                <div class="col-xl-6">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.BLog Page Sub Title') }}</label>
                                                        <input class="primary_input_field"
                                                               placeholder="{{ __('frontendmanage.BLog Sub Title') }}"
                                                               type="text" name="blog_page_sub_title"
                                                               {{ $errors->has('blog_page_sub_title') ? ' autofocus' : '' }}
                                                               value="{{isset($page_content)? $page_content->blog_page_sub_title : ''}}">
                                                    </div>
                                                </div>
                                                <div class="col-xl-2">
                                                    <div class="primary_input mb-25">

                                                        <img class=" imagePreview10" style="max-width: 100%"
                                                             src="{{asset('/'.$page_content->blog_page_banner)}}"
                                                             alt="">

                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="">{{ __('frontendmanage.Blog Page Banner') }}
                                                            <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                        </label>
                                                        <div class="primary_file_uploader">
                                                            <input
                                                                    class="primary-input  filePlaceholder {{ @$errors->has('blog_banner') ? ' is-invalid' : '' }}"
                                                                    type="text" id=""
                                                                    placeholder="Browse file"
                                                                    readonly="" {{ $errors->has('blog_page_banner') ? ' autofocus' : '' }}>
                                                            <button class="" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                       for="file10">{{ __('common.Browse') }}</label>
                                                                <input type="file" class="d-none imgInput10"
                                                                       name="blog_page_banner" id="file10">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-xl-12">
                                                    <hr>
                                                    <br>
                                                </div>

                                            </div>

                                            @if(isModuleActive('LmsSaas'))
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Saas Page Title') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('frontendmanage.Saas Page Title') }}"
                                                                   type="text" name="saas_title"
                                                                   {{ $errors->has('saas_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->saas_title??'' : ''}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Saas Page Sub Title') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('frontendmanage.Saas Page Sub Title') }}"
                                                                   type="text" name="saas_sub_title"
                                                                   {{ $errors->has('saas_sub_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->saas_sub_title??'' : ''}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-2">
                                                        <div class="primary_input mb-25">
                                                            <img class=" imagePreview12" style="max-width: 100%"
                                                                 src="{{asset('/'.@$page_content->saas_banner)}}"
                                                                 alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Saas Plan Banner') }}
                                                                <small>({{__('common.Recommended Size')}} 1920x500)</small>
                                                            </label>
                                                            <div class="primary_file_uploader">
                                                                <input
                                                                        class="primary-input  filePlaceholder {{ @$errors->has('saas_banner') ? ' is-invalid' : '' }}"
                                                                        type="text" id=""
                                                                        placeholder="Browse file"
                                                                        readonly="" {{ $errors->has('saas_banner') ? ' autofocus' : '' }}>
                                                                <button class="" type="button">
                                                                    <label class="primary-btn small fix-gr-bg"
                                                                           for="file12">{{ __('common.Browse') }}</label>
                                                                    <input type="file" class="d-none imgInput12"
                                                                           name="saas_banner" id="file12">
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12">
                                                        <hr>
                                                        <br>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(isModuleActive('CourseOffer'))
                                                <div class="row">

                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Offer Page Title') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('frontendmanage.Offer Page Title') }}"
                                                                   type="text" name="offer_page_title"
                                                                   {{ $errors->has('offer_page_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->offer_page_title : ''}}">
                                                        </div>
                                                    </div>


                                                    <div class="col-xl-6">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Offer Page Sub Title') }}</label>
                                                            <input class="primary_input_field"
                                                                   placeholder="{{ __('frontendmanage.Offer Page Sub Title') }}"
                                                                   type="text" name="offer_page_sub_title"
                                                                   {{ $errors->has('offer_page_sub_title') ? ' autofocus' : '' }}
                                                                   value="{{isset($page_content)? $page_content->offer_page_sub_title : ''}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-2">
                                                        <div class="primary_input mb-25">

                                                            <img class="imagePreview11" style="max-width: 100%"
                                                                 src="{{asset('/'.$page_content->offer_page_banner)}}"
                                                                 alt="">

                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="primary_input mb-25">
                                                            <label class="primary_input_label"
                                                                   for="">{{ __('frontendmanage.Offer Page Banner') }}
                                                                <small>({{__('common.Recommended Size')}}
                                                                    1920x500)</small>
                                                            </label>
                                                            <div class="primary_file_uploader">
                                                                <input
                                                                        class="primary-input  filePlaceholder {{ @$errors->has('offer_banner') ? ' is-invalid' : '' }}"
                                                                        type="text" id=""
                                                                        placeholder="Browse file"
                                                                        readonly="" {{ $errors->has('offer_page_banner') ? ' autofocus' : '' }}>
                                                                <button class="" type="button">
                                                                    <label class="primary-btn small fix-gr-bg"
                                                                           for="file11">{{ __('common.Browse') }}</label>
                                                                    <input type="file" class="d-none imgInput11"
                                                                           name="offer_page_banner" id="file11">
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-xl-12">
                                                        <hr>
                                                        <br>
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


        function readURL11(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview11").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput11").change(function () {
            readURL11(this);
        });
        function readURL12(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview12").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput12").change(function () {
            readURL12(this);
        });
    </script>
@endpush
