@extends('backend.master')
@push('styles')
    <link rel="stylesheet" href="{{asset('public/backend/css/student_list.css')}}"/>
    <style>
        .progress-bar {
            background-color: #9734f2;
        }
    </style>
@endpush
@php
    $table_name='users';
@endphp
@section('table'){{$table_name}}@endsection

@section('mainContent')

     <div class="container-fluid p-0 ">
        <section class="sms-breadcrumb white-box" style="margin-bottom: 80px">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <h1>{{__('courses.Course Statistics')}}</h1>
                    <div class="bc-pages">
                        <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                        <a href="#">{{__('courses.Courses')}}</a>
                        <a href="#">{{__('courses.Course Statistics')}}</a>
                    </div>
                </div>
            </div>
        </section>

             <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>
                                    <tr>
                                        <th scope="col"> {{__('common.SL')}}</th>
                                        <th scope="col">{{__('courses.Course')}}</th>
                                        <th scope="col">{{__('courses.Enrolled')}}</th>
                                        <th scope="col">{{__('common.Pass')}}</th>
                                        <th scope="col">{{__('common.Fail')}}</th>
                                        <th scope="col">{{__('common.Result')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courses as $key => $course)
                                            <tr>
                                                <td>{{@$key+1}}</td>
                                                <td>{{@$course->title}}</td>
                                                <td>{{@$course->enrollUsers->count()}}</td>
                                                <td>{{@$course->result()['complete']}}</td>
                                                <td>{{@$course->result()['incomplete']}}</td>
                                                <td>

                                                    <div class="dropdown CRM_dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                                id="dropdownMenu1{{@$course->id}}" data-toggle="dropdown"
                                                                aria-haspopup="true"
                                                                aria-expanded="false">
                                                            {{ __('common.Select') }}
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right"
                                                             aria-labelledby="dropdownMenu1{{@$course->id}}">
                                                            <a class="dropdown-item" data-toggle="modal" data-target="#view_result{{$course->id}}" href="#">{{__('common.View')}}</a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                             <div class="modal fade admin-query" id="view_result{{$course->id}}">
                                                <div class="modal-dialog modal_1000px modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{__('courses.Course Statistics')}}</h4>
                                                            <button type="button" class="close " data-dismiss="modal">
                                                                <i class="ti-close "></i>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                                <h3 class="text-center">{{$course->title}}</h3>
                                                                <hr>

                                                        <div class="row mt-20 white-box" style="max-height: 500px; overflow:auto">
                                                                @foreach ($course->enrolls as $key => $enroll)
                                                                    <div class="col-lg-12 mt-2 d-flex">
                                                                            @php
                                                                                $percentage=round($course->userTotalPercentage($enroll->user_id,$enroll->course_id));
                                                                                if ($percentage < 100) {
                                                                                    $status='Fail';
                                                                                } else {
                                                                                    $status='Pass';
                                                                                }
                                                                            @endphp
                                                                                <div class="col-lg-2">
                                                                                    {{$key+1}}
                                                                                </div>
                                                                                <div class="col-lg-6">
                                                                                    {{$enroll->user->name}}
                                                                                </div>
                                                                                <div class="col-lg-4">

                                                                                    <button class="primary-btn radius_30px mr-10 fix-gr-bg" > {{$status}}</button>
                                                                                </div>
                                                                    </div>
                                                                    @endforeach


                                                        </div>

                                                        <div class="col-lg-12 text-center pt_15">
                                                            <div class="d-flex justify-content-center">
                                                                <button class="primary-btn semi_large2  fix-gr-bg" data-dismiss="modal"
                                                                        type="button"><i
                                                                        class="ti-check"></i> {{__('common.Close')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

@endsection
@push('scripts')

    @if ($errors->any())
        <script>
            @if(Session::has('type'))
            @if(Session::get('type')=="store")
            $('#add_student').modal('show');
            @else
            $('#editStudent').modal('show');
            @endif
            @endif
        </script>
    @endif


@endpush
