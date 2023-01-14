@extends('backend.master')
@php
    $table_name='course_levels';
@endphp
@section('table'){{$table_name}}@endsection
@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('courses.Level List')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('courses.Course')}}</a>
                    <a class="active" href="{{route('course-level.index')}}">{{__('courses.Level')}}</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px"> @if(!isset($edit)) {{__('courses.Add New Level') }} @else {{__('courses.Update Level')}} @endif</h3>
                        </div>
                    </div>
                    <div class="white-box mb_30">
                        @if (isset($edit))
                            @if (permissionCheck('course-level.update'))
                            <form action="{{route('course-level.update',$edit->id)}}" method="POST" id="category-form"
                                  name="category-form" enctype="multipart/form-data">
                                @endif
                                <input type="hidden" name="id"
                                       value="{{@$edit->id}}">
                                @method('PATCH')
                                @else
                                    @if (permissionCheck('course-level.store'))
                                        <form action="{{route('course-level.store') }}" method="POST"
                                              id="category-form" name="category-form" enctype="multipart/form-data">
                                            @endif
                                            @endif

                                            @csrf

                                            <div class="row">


                                                <div class="col-xl-12">
                                                    <div class="primary_input mb-25">
                                                        <label class="primary_input_label"
                                                               for="nameInput">{{ __('common.Title') }} <strong
                                                                class="text-danger">*</strong></label>
                                                        <input name="title" id="nameInput"
                                                               class="primary_input_field title {{ @$errors->has('title') ? ' is-invalid' : '' }}"
                                                               placeholder="{{ __('common.Title') }}" type="text"
                                                               value="{{isset($edit)?@$edit->title:old('title')}}">
                                                        @if ($errors->has('title'))
                                                            <span class="invalid-feedback d-block mb-10" role="alert">
                                            <strong>{{ @$errors->first('title') }}</strong>
                                        </span>
                                                        @endif
                                                    </div>
                                                </div>


                                                @php
                                                    $tooltip = "";
                                                    if(permissionCheck('course-level.store')){
                                                          $tooltip = "";
                                                      }else{
                                                          $tooltip = trans('courses.You have no permission to add');
                                                      }
                                                @endphp
                                                <div class="col-lg-12 text-center">
                                                    <div class="d-flex justify-content-center pt_20">
                                                        <button type="submit" class="primary-btn semi_large fix-gr-bg"
                                                                data-toggle="tooltip" title="{{@$tooltip}}"
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
                <div class="col-lg-9">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0">{{__('courses.Level List')}}</h3>
                        </div>
                    </div>
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{ __('common.SL') }}</th>
                                        <th scope="col">{{ __('common.Title') }}</th>
                                        <th scope="col">{{ __('common.Status') }}</th>
                                        <th scope="col">{{ __('common.Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($levels as $key => $level)
                                        <tr>
                                            <th class="m-2">{{ $key+1 }}</th>
                                            <td>{{@$level->title }}</td>


                                            <td class="nowrap">
                                                <label class="switch_toggle" for="active_checkbox{{@$level->id }}">
                                                    <input type="checkbox"
                                                           class="   status_enable_disable  "
                                                           id="active_checkbox{{@$level->id }}"
                                                           @if (@$level->status == 1) checked
                                                           @endif value="{{@$level->id }}">
                                                    <i class="slider round"></i>
                                                </label>
                                            </td>

                                            <td>
                                                <!-- shortby  -->
                                                <div class="dropdown CRM_dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu1{{ @$level->id }}"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        {{ __('common.Select') }}
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         aria-labelledby="dropdownMenu1{{ @$level->id }}">
                                                        @if (permissionCheck('course-level.update'))
                                                            <a class="dropdown-item edit_brand"
                                                               href="{{route('course-level.edit',@$level->id)}}">{{__('common.Edit')}}</a>
                                                        @endif
                                                        @if (permissionCheck('course-level.destroy'))
                                                            <a onclick="confirm_modal('{{route('course-level.destroy', @$level->id)}}');"
                                                               class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- shortby  -->
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="edit_form">

    </div>
    <div id="view_details">

    </div>


    @include('backend.partials.delete_modal')
@endsection
@push('scripts')
    {{--    <script src="{{asset('public/backend/js/category.js')}}"></script>--}}
@endpush
