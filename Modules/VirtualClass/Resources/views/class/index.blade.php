@extends('backend.master')
@push('styles')
    <link rel="stylesheet" href="{{asset('public/backend/css/class.css')}}"/>
@endpush
@php
    $table_name='courses';
@endphp
@section('table'){{$table_name}}@stop
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1> {{__('virtual-class.Class List')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#"> {{__('virtual-class.Class List')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if(isset($bank))
                @if (permissionCheck('virtual-class.store'))
                    <div class="row">
                        <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                            <a href="{{route('virtual-class')}}" class="primary-btn small fix-gr-bg">
                                <span class="ti-plus pr-2"></span>
                                {{__('common.Add')}}
                            </a>
                        </div>
                    </div>
                @endif
            @endif
            <div class="row">
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-20">
                                    @if(isset($class))
                                        {{__('common.Edit')}}
                                    @else
                                        {{__('common.Add')}}
                                    @endif
                                    {{__('virtual-class.Class')}}
                                </h3>
                            </div>

                            @if(isset($class))

                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => array('virtual-class.update',$class->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'question_bank']) }}

                            @else
                                @if (permissionCheck('virtual-class.create'))

                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'virtual-class.store',
                                    'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'question_bank']) }}

                                @endif
                            @endif
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <label> {{__('virtual-class.Title')}} *</label>
                                                <input type="text" placeholder="{{__('virtual-class.Title')}}"
                                                       class="primary_input_field name{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                                       name="title" {{ $errors->has('title') ? ' autofocus' : '' }}
                                                       value="{{isset($class)? $class->title:(old('title')!=''?(old('title')):'')}}">
                                                <span class="focus-border textarea"></span>
                                                @if ($errors->has('title'))
                                                    <span
                                                        class="error text-danger"><strong>{{ $errors->first('title') }}</strong></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->role_id==1)
                                        <div class="row mt-25">
                                            <div class="col-xl-12">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="assign_instructor">{{__('courses.Assign Instructor')}} </label>
                                                    <select class="primary_select category_id" name="assign_instructor"
                                                            id="assign_instructor" {{$errors->has('assign_instructor') ? 'autofocus' : ''}}>
                                                        <option
                                                            data-display="{{__('common.Select')}} {{__('courses.Instructor')}}"
                                                            value="">{{__('common.Select')}} {{__('courses.Instructor')}} </option>
                                                        @foreach($instructors as $instructor)
                                                            <option
                                                                value="{{$instructor->id}}" {{isset($class)?$instructor->id==$class->course->user_id?'selected':'':''}}>{{@$instructor->name}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row mt-25">
                                        <div class="col-xl-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                       for="assistant_instructors">{{__('courses.Assistant Instructor')}} </label>
                                                <select name="assistant_instructors[]" id="assistant_instructors"
                                                        class="multypol_check_select active mb-15 e1"
                                                        multiple>
                                                    @foreach ($instructors as $instructor)
                                                        <option value="{{$instructor->id}}" {{isset($class)&&!empty($class->course->assistantInstructorsIds) && in_array($instructor->id,$class->course->assistantInstructorsIds)?'selected':''}}>{{@$instructor->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <label> {{__('virtual-class.Duration')}} {{__('virtual-class.Per Class')}}
                                                    ({{__('virtual-class.in Minute')}}) *</label>
                                                <input {{ $errors->has('duration') ? ' autofocus' : '' }}
                                                       class="primary_input_field name{{ $errors->has('duration') ? ' is-invalid' : '' }}"
                                                       type="number" name="duration" placeholder="e.g.30min"
                                                       value="{{isset($class)? $class->duration:(old('duration')!=''?(old('duration')):'')}}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('duration'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('duration') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <label class="primary_input_label" for="">{{__('quiz.Category')}}</label>
                                            <select {{ $errors->has('category') ? ' autofocus' : '' }}
                                                    class="primary_select {{ $errors->has('category') ? ' is-invalid' : '' }}"
                                                    id="category_id" name="category">
                                                <option data-display=" {{__('quiz.Category')}} *"
                                                        value=""> {{__('quiz.Category')}} *
                                                </option>
                                                @foreach($categories as $category)
                                                    @if(isset($class))
                                                        <option
                                                            value="{{$category->id}}" {{$class->category_id == $category->id? 'selected': ''}}>{{$category->name}}</option>
                                                    @else
                                                        <option
                                                            value="{{$category->id}}" {{old('category')!=''? (old('category') == $category->id? 'selected':''):''}}>{{$category->name}}</option>
                                                    @endif

                                                @endforeach
                                            </select>
                                            @if ($errors->has('category'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('category') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-25">
                                        <div class="col-lg-12 mt-30-md" id="subCategoryDiv">
                                            <label class="primary_input_label"
                                                   for="">{{__('quiz.Sub Category')}}</label>
                                            <select {{ $errors->has('sub_category') ? ' autofocus' : '' }}
                                                    class="primary_select{{ $errors->has('sub_category') ? ' is-invalid' : '' }} select_section"
                                                    id="subcategory_id" name="sub_category">
                                                <option
                                                    data-display=" {{__('common.Select')}} {{__('quiz.Sub Category')}}"
                                                    value="">{{__('common.Select')}} {{__('quiz.Sub Category')}}
                                                </option>

                                                @if(isset($class))
                                                    <option value="{{@$class->sub_category_id}}"
                                                            selected>{{@$class->subCategory->name}}</option>
                                                @endif
                                            </select>
                                            @if ($errors->has('sub_category'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('sub_category') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <div class="checkbox_wrap d-flex align-items-center">
                                                <label for="edit_course"
                                                       class="switch_toggle">
                                                    <input type="checkbox" name="free"
                                                           {{isset($class) && $class->fees == 0 ? 'checked' : ''}} class="free_class"
                                                           id="edit_course"
                                                           value="0">
                                                    <i class="slider round"></i>
                                                </label>
                                                <label>{{__('virtual-class.This class is free')}}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25 fees"
                                         @if (isset($class) && $class->fees == 0) style="display:none;" @endif>
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <label> {{__('virtual-class.Fees')}} *</label>
                                                <input
                                                    class="primary_input_field name{{ $errors->has('fees') ? ' is-invalid' : '' }}"
                                                    type="number" name="fees"
                                                    value="{{isset($class)? $class->fees:(old('fees')!=''?(old('fees')):0)}}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('fees'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('fees') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25">
                                        <div class="col-xl-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">{{__('common.Image')}}
                                                    *</label>
                                                <div class="primary_file_uploader">
                                                    <input class="primary-input filePlaceholder" type="text"
                                                           placeholder="{{isset($class) && $class->image ? showPicName($class->image) :__('virtual-class.Browse Image file')}}"
                                                           readonly="" {{ $errors->has('image') ? ' autofocus' : '' }}>
                                                    <button class="" type="button">
                                                        <label class="primary-btn small fix-gr-bg"
                                                               for="document_file">{{__('common.Browse')}}</label>
                                                        <input type="file" class="d-none fileUpload" name="image"
                                                               id="document_file">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 mt-25">
                                            <label>{{__('courses.View Scope')}} </label>
                                            <select class="primary_select " name="scope"
                                                    id="">
                                                <option
                                                    value="1" {{@$class->course->scope=="1"?'selected':''}}>{{__('courses.Public')}}
                                                </option>

                                                <option {{@$class->course->scope=="0"?'selected':''}} value="0">
                                                    {{__('courses.Private')}}
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <label class="primary_input_label"
                                                   for="">{{__('virtual-class.Language')}} *</label>
                                            <select class="primary_select" name="lang_id" id="">
                                                <option
                                                    data-display="{{ __('common.Select') }} {{ __('common.Language') }}"
                                                    value="">{{ __('common.Select') }} {{ __('common.Language') }}</option>
                                                @foreach ($languages as $language)
                                                    <option
                                                        value="{{$language->id}}"
                                                        @if(!isset($class)) @if($language->id==19) selected @endif @endif{{isset($class) && $class->lang_id == $language->id ? 'selected' : ''}} >{{$language->native}}</option>

                                                @endforeach
                                            </select>
                                            @if ($errors->has('lang_id'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('lang_id') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mt-25    @if(isset($class)) d-none @endif">
                                        <div class="col-lg-12">
                                            <label class="primary_input_label"
                                                   for="">{{__('virtual-class.Type')}}</label>
                                            <select
                                                class="primary_select type {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                                id="type" name="type">
                                                <option
                                                    value="0" {{isset($class) && $class->type == 0 ? 'selected' : old('type')}}>{{__('virtual-class.Single Class')}}</option>
                                                <option
                                                    value="1" {{isset($class) && $class->type == 1 ? 'selected' : old('type')}}>{{__('virtual-class.Continuous Class')}}</option>
                                            </select>
                                            @if ($errors->has('type'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('type') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div @if(!isset($class) || $class->type == 0) style="display: none"
                                         @endif class="row mt-25 continuous_class ">
                                        <div class="col-xl-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                       for="">{{ __('coupons.Start Date') }}</label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input placeholder="Start Date"
                                                                       class="primary_input_field primary-input date form-control  {{ @$errors->has('start_date') ? ' is-invalid' : '' }}"
                                                                       id="start_date" type="text"
                                                                       name="start_date"
                                                                       value="{{isset($class)? date('m/d/Y', strtotime($class->start_date)) : date('m/d/Y')}}"
                                                                       autocomplete="off">

                                                            </div>
                                                        </div>
                                                        <button class="" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                    @if ($errors->has('start_date'))
                                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('start_date') }}</strong>
                                                </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 mt-25">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                       for="">{{ __('virtual-class.End Date') }}</label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input placeholder="End Date"
                                                                       class="primary_input_field primary-input date form-control  {{ @$errors->has('end_date') ? ' is-invalid' : '' }}"
                                                                       id="end_date" type="text"
                                                                       name="end_date"
                                                                       value="{{isset($class)?  date('m/d/Y', strtotime($class->end_date)) : date('m/d/Y')}}"
                                                                       autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <button class="" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                    @if ($errors->has('end_date'))
                                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('end_date') }}</strong>
                                                </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div @if(isset($class) && $class->type == 1) style="display: none"
                                         @endif class="row mt-25 single_class  ">
                                        <div class="col-xl-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                       for="">{{ __('virtual-class.Date') }}</label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input placeholder="Date" readonly
                                                                       class="primary_input_field primary-input date form-control  {{ @$errors->has('date') ? ' is-invalid' : '' }}"
                                                                       id="start_date" type="text"
                                                                       name="date"
                                                                       value="{{isset($class) && $class->type == 0 ? date('m/d/Y', strtotime($class->start_date)) : date('m/d/Y')}}"
                                                                       autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <button class="" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                    @if ($errors->has('start_date'))
                                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('start_date') }}</strong>
                                                </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <label>{{__('virtual-class.Time')}} <span>*</span></label>
                                            <div class="primary_input">
                                                <input required
                                                       class="primary-input primary_input_field  time form-control{{ @$errors->has('time') ? ' is-invalid' : '' }}"
                                                       type="text" name="time"
                                                       value="{{ isset($class) ? old('time',$class->time): old('time')}}">

                                            </div>

                                            @if ($errors->has('time'))
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ @$errors->first('time') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row @if(isset($class)) d-none @endif">
                                        <div class="col-md-12 mt-25 ">
                                            <label class="primary_input_label"
                                                   for=""> {{__('virtual-class.Host')}} </label>
                                        </div>

                                        <div class="col-md-6 mb-25">
                                            <label for="type1" class="primary_checkbox d-flex mr-12 ">
                                                <input type="radio" class="common-checkbox" id="type1" name="host"
                                                       value="Zoom"
                                                       @if(isset($class)) @if($class->host=="Zoom") checked @endif @else
                                                       checked @endif>
                                                <span class="checkmark mr-2"></span>{{__('virtual-class.Zoom')}}</label>
                                        </div>

                                        @if(isModuleActive("BBB"))
                                            <div class="col-md-6 mb-25">
                                                <label for="type2" class="primary_checkbox d-flex mr-12 ">
                                                    <input type="radio" class="common-checkbox" id="type2" name="host"
                                                           value="BBB"
                                                           @if(isset($class)) @if($class->host=="BBB") checked @endif @endif
                                                    >
                                                    <span class="checkmark mr-2"></span> {{__('virtual-class.BBB')}}
                                                </label>
                                            </div>
                                        @endif

                                        @if(isModuleActive("Jitsi"))
                                            <div class="col-md-6 mb-25">
                                                <label for="type3" class="primary_checkbox d-flex mr-12 ">
                                                    <input type="radio" class="common-checkbox" id="type3" name="host"
                                                           value="Jitsi"
                                                           @if(isset($class)) @if($class->host=="Jitsi") checked @endif @endif
                                                    >
                                                    <span class="checkmark mr-2"></span> {{__('jitsi.Jitsi')}}</label>
                                            </div>
                                        @endif
                                    </div>


                                    <div class=" mt-25 single_class zoomSetting @if(isset($class)) d-none @endif"
                                         style="display: {{ isset($class) ? $class->host=="Zoom"? "block":"none": 'block'}}">

                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                           for="password">{{ __('zoom.Password') }} *</label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">
                                                                    <input placeholder="Password"
                                                                           class="primary_input_field primary-input   form-control"
                                                                           id="password" type="text"
                                                                           name="password"
                                                                           value="123456"
                                                                           autocomplete="off">
                                                                </div>
                                                            </div>

                                                        </div>
                                                        @if ($errors->has('password'))
                                                            <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('password') }}</strong>
                                                </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-xl-12  mt-25">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                           for="password">{{__('zoom.Recurring')}}</label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-25">
                                                                    <div class="mr-30">
                                                                        <label class="primary_checkbox d-flex mr-12 "
                                                                               for="recurring_options1">
                                                                            <input type="radio" name="is_recurring"
                                                                                   id="recurring_options1"
                                                                                   value="1"
                                                                                   class="common-radio recurring-type">
                                                                            <span
                                                                                class="checkmark mr-2"></span> {{__('zoom.Yes')}}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-25">
                                                                    <div class="mr-30">
                                                                        <label class="primary_checkbox d-flex mr-12 "
                                                                               for="recurring_options2">
                                                                            <input type="radio" name="is_recurring"
                                                                                   id="recurring_options2"
                                                                                   value="0"
                                                                                   checked
                                                                                   class="common-radio recurring-type">
                                                                            <span
                                                                                class="checkmark mr-2"></span> {{__('zoom.No')}}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                        @if ($errors->has('is_recurring'))
                                                            <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('is_recurring') }}</strong>
                                                </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class=" zoom-recurrence-section-hide">
                                            <div class="row">
                                                <div class="col-xl-12  mt-25">
                                                    <select {{ $errors->has('recurring_type') ? ' autofocus' : '' }}
                                                            class="primary_select {{ $errors->has('recurring_type') ? ' is-invalid' : '' }}"
                                                            id="recurring_type" name="recurring_type">
                                                        <option data-display="{{__('zoom.Recurrence type')}} *"
                                                                value="">{{__('zoom.Student')}}
                                                            {{__('zoom.Recurrence type')}} *
                                                        </option>
                                                        <option
                                                            value="1" {{ old('recurring_type') == 1  ? 'selected':''}} > {{__('zoom.Daily')}}
                                                        </option>
                                                        <option
                                                            value="2" {{ old('recurring_type') == 2  ? 'selected':''}} > {{__('zoom.Weekly')}}
                                                        </option>
                                                        <option
                                                            value="3" {{ old('recurring_type') == 3  ? 'selected':''}}>  {{__('zoom.Monthly')}}
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('recurring_type'))
                                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('recurring_type') }}</strong>
                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-12  mt-25">
                                                    <select
                                                        {{ $errors->has('recurring_repect_day') ? ' autofocus' : '' }}
                                                        class="primary_select {{ $errors->has('recurring_repect_day') ? ' is-invalid' : '' }}"
                                                        id="recurring_repect_day" name="recurring_repect_day">
                                                        <option data-display=" Select *"
                                                                value="">{{__('zoom.Zoom Recurring Repeat')}}*
                                                        </option>
                                                        @for ($i = 1; $i <= 15; $i++)
                                                            <option
                                                                value="{{ $i }}" {{ old('recurring_repect_day') == $i ? 'selected':''}} >{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    @if ($errors->has('recurring_type'))
                                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('recurring_type') }}</strong>
                                        </span>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-xl-12  mt-25">
                                                    <div class="primary_input">
                                                        <div class="primary_datepicker_input">
                                                            <div class="no-gutters input-right-icon">
                                                                <div class="col">
                                                                    <div class="">
                                                                        <input id="recurring_end_date"
                                                                               class="primary_input_field primary-input date form-control "
                                                                               placeholder="{{__('zoom.Recurring End')}}"
                                                                               type="text" name="recurring_end_date"
                                                                               value="{{isset($class)? $class->start_date : date('m/d/Y')}}"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <button class="" type="button">
                                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                                </button>
                                                            </div>
                                                            @if ($errors->has('recurring_end_date'))
                                                                <span class="invalid-feedback d-block mb-10"
                                                                      role="alert">
                                                <strong>{{ @$errors->first('recurring_end_date') }}</strong>
                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-25">
                                            <div class="col-xl-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                           for="">{{__('zoom.Attached File')}}
                                                    </label>
                                                    <div class="primary_file_uploader">
                                                        <input class="primary-input filePlaceholder" type="text"
                                                               placeholder="{{isset($editdata->attached_file) && @$editdata->attached_file != ""? getFilePath3(@$editdata->attached_file) : trans('zoom.Attached File')}}"

                                                               readonly="" {{ $errors->has('attached_file') ? ' autofocus' : '' }}>
                                                        <button class="" type="button">
                                                            <label class="primary-btn small fix-gr-bg"
                                                                   for="attached_file">{{__('common.Browse')}}</label>
                                                            <input type="file" class="d-none fileUpload"
                                                                   name="attached_file"
                                                                   id="attached_file">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class=" mt-25 single_class bbbSetting @if(isset($class)) d-none @endif"
                                         style="display: {{ isset($class) ? $class->host=="BBB"? "block":"none": 'none'}}">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                           for="">{{__('bbb.Attendee Password')}}
                                                    </label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('attendee_password') ? ' is-invalid' : '' }}"
                                                        type="text" name="attendee_password"
                                                        autocomplete="off"
                                                        placeholder="{{__('bbb.Attendee Password')}}"
                                                        value="{{ isset($editdata) ?  old('topic',$editdata->attendee_password) : old('attendee_password','12345678') }}">

                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('attendee_password'))
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('attendee_password') }}</strong>
                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-25">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                           for="">{{__('bbb.Moderator Password')}}
                                                    </label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('moderator_password') ? ' is-invalid' : '' }}"
                                                        type="text" name="moderator_password"
                                                        placeholder="{{__('bbb.Moderator Password')}}"
                                                        autocomplete="off"
                                                        value="{{ isset($editdata) ?  old('topic',$editdata->moderator_password) : old('moderator_password','123456') }}">

                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('moderator_password'))
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('moderator_password') }}</strong>
                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class=" mt-25 single_class jitsiSetting @if(isset($class)) d-none @endif"
                                         style="display: {{ isset($class) ? $class->host=="Jitsi"? "block":"none": 'none'}}">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                           for="">{{__('jitsi.Meeting ID/Room')}}
                                                    </label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('jitsi_meeting_id') ? ' is-invalid' : '' }}"
                                                        type="text" name="jitsi_meeting_id"
                                                        autocomplete="off"
                                                        placeholder="{{__('jitsi.Meeting ID/Room')}}"
                                                        value="{{date('ymdhmi')}}">

                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('jitsi_meeting_id'))
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('jitsi_meeting_id') }}</strong>
                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="row mt-25 ">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                       for="">{{__('jitsi.Description')}}
                                                </label>
                                                <textarea class="primary_input_field form-control"
                                                          cols="0"
                                                          rows="4"
                                                          placeholder="{{__('jitsi.Description')}}"
                                                          name="description"
                                                          id="address">{{isset($class) ? old('description',$class->course->about??'') : old('description')}}</textarea>


                                                @if ($errors->has('description'))
                                                    <span class="invalid-feedback" role="alert">
                                                          <strong>{{ $errors->first('description') }}</strong>
                                                      </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if(Settings('frontend_active_theme')=="edume")
                                        <div class="row mt-25">
                                            <div class="col-xl-12">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{__('courses.Key Point') }} (1)</label>
                                                    <input class="primary_input_field" name="what_learn1"
                                                           placeholder="-"
                                                           type="text"
                                                           value="{{isset($class) ? old('what_learn1',$class->course->what_learn1??'') : old('what_learn1')}}">
                                                </div>
                                            </div>

                                            <div class="col-xl-12">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{__('courses.Key Point') }} (2) </label>
                                                    <input class="primary_input_field" name="what_learn2"
                                                           placeholder="-"
                                                           type="text"
                                                           value="{{isset($class) ? old('what_learn2',$class->course->what_learn2??'') : old('what_learn2')}}">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <label class="primary_input_label"
                                                   for="certificate">{{__('certificate.Certificate')}}</label>
                                            <div class="primary_input">
                                                <select class="primary_select "
                                                        name="certificate" id="certificate">
                                                    <option
                                                        data-display="{{__('common.Select')}} {{__('certificate.Certificate')}}"
                                                        value="">{{__('common.Select')}} {{__('certificate.Certificate')}} </option>
                                                    @foreach($certificates as $certificate)
                                                        <option value="{{$certificate->id}}"
                                                            {{isset($class) ? $certificate->id==$class->course->certificate_id?'selected':'' : ''}}
                                                        >{{@$certificate->title}} </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25">
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="primary-btn fix-gr-bg"
                                                    data-toggle="tooltip">
                                                <span class="ti-check"></span>
                                                @if(isset($class))
                                                    {{__('common.Update')}}
                                                @else
                                                    {{__('common.Save')}}
                                                @endif
                                                {{__('virtual-class.Class')}}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 ">
                    <div class="main-title">
                        <h3 class="mb-20">{{__('virtual-class.Class List')}}</h3>
                    </div>

                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>
                                    @if(session()->has('message-success-delete') != "" ||
                                    session()->get('message-danger-delete') != "")
                                        <tr>
                                            <td colspan="5">
                                                @if(session()->has('message-success-delete'))
                                                    <div class="alert alert-success">
                                                        {{ session()->get('message-success-delete') }}
                                                    </div>
                                                @elseif(session()->has('message-danger-delete'))
                                                    <div class="alert alert-danger">
                                                        {{ session()->get('message-danger-delete') }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>{{__('common.SL')}}</th>
                                        <th>{{__('virtual-class.Title')}}</th>
                                        <th>{{__('virtual-class.Category')}}</th>
                                        <th>{{__('virtual-class.Sub Category')}}</th>
                                        <th>{{__('virtual-class.Language')}}</th>
                                        <th scope="col">{{__('common.Status')}}</th>

                                        <th>{{__('virtual-class.Duration')}}</th>
                                        <th>{{__('virtual-class.Fees')}}</th>
                                        <th>{{__('virtual-class.Type')}}</th>
                                        <th>{{__('virtual-class.Start Date')}}</th>
                                        <th>{{__('virtual-class.End Date')}}</th>
                                        <th>{{__('virtual-class.Time')}}</th>
                                        <th>{{__('virtual-class.Host')}}</th>
                                        <th>{{__('courses.View Scope')}}</th>
                                        <th>{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>


                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade admin-query" id="deleteClass">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('common.Delete')}}  </h4>
                    <button type="button" class="close" data-dismiss="modal"><i
                            class="ti-close "></i></button>
                </div>

                <div class="modal-body">
                    <form action="{{route('virtual-class.destroy')}}" method="post">
                        @csrf

                        <div class="text-center">

                            <h4>{{__('common.Are you sure to delete ?')}} </h4>
                        </div>
                        <input type="hidden" name="id" value="" id="classDeleteId">
                        <div class="mt-40 d-flex justify-content-between">
                            <button type="button" class="primary-btn tr-bg"
                                    data-dismiss="modal">{{__('common.Cancel')}}</button>

                            <button class="primary-btn fix-gr-bg"
                                    type="submit">{{__('common.Delete')}}</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')

    @php
        $url = route('getAllVirtualClassData');
    @endphp

    <script>
        let table = $('#lms_table').DataTable({
            bLengthChange: false,
            "bDestroy": true,
            processing: true,
            serverSide: true,
            order: [[0, "desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: '{!! $url !!}',
                pages: 5 // number of pages to cache
            }),
            columns: [
                {data: 'DT_RowIndex', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'category_name', name: 'category.name'},
                {data: 'subCategory', name: 'subCategory.name', orderable: false},
                {data: 'language', name: 'language.name'},
                {data: 'status', name: 'status', orderable: false},
                {data: 'duration', name: 'duration'},
                {data: 'fees', name: 'fees'},
                {data: 'type', name: 'type'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'time', name: 'time'},
                {data: 'host', name: 'host'},
                {data: 'scope', name: 'scope'},
                {data: 'action', name: 'action', orderable: false},

            ],
            language: {
                emptyTable: "{{ __("common.No data available in the table") }}",
                search: "<i class='ti-search'></i>",
                searchPlaceholder: '{{ __("common.Quick Search") }}',
                paginate: {
                    next: "<i class='ti-arrow-right'></i>",
                    previous: "<i class='ti-arrow-left'></i>"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="far fa-copy"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: '{{ __("common.Copy") }}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="far fa-file-excel"></i>',
                    titleAttr: '{{ __("common.Excel") }}',
                    title: $("#logo_title").val(),
                    margin: [10, 10, 10, 0],
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    },

                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="far fa-file-alt"></i>',
                    titleAttr: '{{ __("common.CSV") }}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="far fa-file-pdf"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: '{{ __("common.PDF") }}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    margin: [0, 0, 0, 12],
                    alignment: 'center',
                    header: true,
                    customize: function (doc) {
                        doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }

                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: '{{ __("common.Print") }}',
                    title: $("#logo_title").val(),
                    exportOptions: {
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    postfixButtons: ['colvisRestore']
                }
            ],
            columnDefs: [{
                visible: false
            }],
            responsive: true,
        });


        $(document).on('click', '.deleteClass', function () {
            let id = $(this).data('id');
            $('#classDeleteId').val(id);
            $("#deleteClass").modal('show');
        });
    </script>>

    <script src="{{asset('/')}}/Modules/CourseSetting/Resources/assets/js/course.js"></script>
    <script src="{{asset('public/backend/js/zoom.js')}}"></script>




@endpush
