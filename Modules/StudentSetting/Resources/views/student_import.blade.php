@extends('backend.master')
@push('styles')
    <link rel="stylesheet" href="{{asset('public/backend/css/student_list.css')}}"/>
@endpush
@php
    $table_name='users';
@endphp
@section('table'){{$table_name}}@endsection

@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('student.Students')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#">{{__('student.Students')}}</a>
                    <a href="#">{{__('student.Import Student')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-6">
                    <div class="main-title">
                        <h3>{{__('student.Import Student')}}</h3>
                    </div>
                </div>
                <div class="offset-lg-2 col-lg-4 text-right mb-20">
                    <a href="{{route('country_list_download')}}">
                        <button class="primary-btn tr-bg text-uppercase bord-rad">
                            {{__('common.Country List')}} {{__('common.Download')}}
                            <span class="pl ti-download"></span>
                        </button>
                    </a>
                    <a href="{{route('student_excel_download')}}">
                        <button class="primary-btn tr-bg text-uppercase bord-rad">
                            {{__('common.Download')}}
                            <span class="pl ti-download"></span>
                        </button>
                    </a>
                </div>

            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student_import_save',
                                'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'student_form']) }}
            <div class="row">
                <div class="col-lg-12">


                    <div class="white-box">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-title">

                                    </div>
                                </div>
                            </div>
                            <div>
                                <ul>
                                    @if ($custom_field->show_gender==1)
                                        <li style="list-style: auto;">
                                            Use 'male' for Male ,'female' for Female & 'other' for Other
                                        </li>
                                    @endif
                                    @if ($custom_field->show_student_type==1)
                                        <li style="list-style: auto;">
                                            Use 'personal' for Personal & 'corporate' for Corporate
                                        </li>
                                    @endif
                                    <li style="list-style: auto;">
                                        Use 'dd-mm-yyyy' format for Date of birth
                                    </li>
                                    <li style="list-style: auto;">
                                        Use 'id' for country. For getting country ID Download Country List
                                    </li>
                                </ul>
                            </div>

                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="row mb-40 mt-30">


                                <div class="col-lg-6">

                                    <label class="primary_input_label" for="course">{{__('courses.Course')}}
                                        <strong class="text-danger">*</strong> </label>
                                    <select class="primary_select" name="course" id="course">
                                        <option data-display="{{__('common.Select')}} {{__('courses.Course')}}"
                                                value="">{{__('common.Select')}} {{__('courses.Course')}}</option>
                                        @foreach($courses as $course)
                                            <option
                                                value="{{$course->id}}">{{@$course->title}} </option>
                                        @endforeach
                                    </select>
                                    {{-- @if ($errors->has('course'))
                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                            <strong>{{ @$errors->first('course') }}</strong>
                                        </span>
                                    @endif --}}
                                </div>

                                <div class="col-lg-6">
                                    <div class="primary_input mb-35">
                                        <label class="primary_input_label"
                                               for="">{{__('common.Browse')}} CSV/Excel <strong
                                                class="text-danger">*</strong> </label>
                                        <div class="primary_file_uploader">
                                            <input class="primary-input" type="text" id="placeholderFileOneName"
                                                   placeholder="{{__('common.Browse')}}  CSV/Excel" readonly="">
                                            <button class="primary_btn_2" type="button">
                                                <label class="primary_btn_2"
                                                       for="document_file_1">{{__('common.Browse')}} </label>
                                                <input type="file" class="d-none" name="file" id="document_file_1">
                                            </button>
                                        </div>
                                    </div>
                                    {{-- @if ($errors->has('file'))
                                    <span class="invalid-feedback d-block mb-10" role="alert">
                                        <strong>{{ @$errors->first('file') }}</strong>
                                    </span>
                                @endif --}}
                                </div>

                            </div>

                            <div class="row mt-40">
                                <div class="col-lg-12 text-center">
                                    <button class="primary-btn fix-gr-bg">
                                        <span class="ti-check"></span>
                                        {{__('student.Import Student')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </section>

@endsection

