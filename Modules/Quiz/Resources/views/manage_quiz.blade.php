@extends('backend.master')
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('quiz.Quiz')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="{{ route('online-quiz') }}">{{__('quiz.Quiz')}}</a>
                    <a href="{{route("manage_online_exam_question", [$online_exam->id])}}"> {{__('quiz.Quiz Question')}} </a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-lg-12 mb-30">
                    <div class="white_box mb_20">
                        <div class="white_box_tittle list_header">
                            <h4>{{__('quiz.filterBy')}} </h4>
                        </div>
                        <form action="" method="GET">
                            <div class="row">

                                <div class="col-lg-6 mt-20">

                                    <select class="primary_select" name="group" id="">
                                        <option data-display="{{__('quiz.selectGroup')}}"
                                                value=""> {{__('quiz.selectGroup')}}</option>
                                        @foreach($groups as $group)
                                            <option
                                                value="{{$group->id}}" {{$group->id==$searchGroup?'selected':''}}> {{$group->title}}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-lg-6 mt-1">
                                    <div class="search_course_btn ">
                                        <br>
                                        <button type="submit"
                                                class="primary-btn radius_30px mr-10 fix-gr-bg">{{__('courses.Filter')}} </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8 mt--1">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3>{{__('quiz.Question List')}}
                                </h3>
                            </div>
                        </div>
                    </div>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'online_exam_question_assign',
                            'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'student_form']) }}
                    <input type="hidden" id="online_exam_id" name="online_exam_id" value="{{ @$online_exam->id}}">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div class="row">
                                <div class="col-md-12">
                                    Auto result only allow in MCQ
                                </div>
                            </div>
                            <table id="lms_table" class="table quiz_assign_table">
                                <thead>
                                @if(session()->has('message-success') != "" ||
                                session()->get('message-danger') != "")
                                    <tr>
                                        <td colspan="6">
                                            @if(session()->has('message-success'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success') }}
                                                </div>
                                            @elseif(session()->has('message-danger'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>


                                        <label class="primary_checkbox d-flex  "
                                               for="questionSelectAll">
                                            <input type="checkbox"
                                                   id="questionSelectAll"
                                                   value=""
                                                   @if(count($question_banks)==count($already_assigned)) checked @endif
                                                   class="common-checkbox selectAllQuiz">
                                            <span class="checkmark"></span>
                                        </label>
                                    </th>
                                    <th> {{__('quiz.Group')}} </th>
                                    <th>{{__('quiz.Question Type')}}</th>
                                    <th>{{__('quiz.Question')}}</th>
                                    <th>{{__('quiz.Marks')}}</th>
                                    <th>{{__('quiz.Image')}}</th>
{{--                                    <th>{{__('common.Action')}}</th>--}}
                                </tr>
                                </thead>
                                <tbody>

                                @php $total_marks = 0; @endphp
                                @foreach($question_banks as $question_bank)

                                    @php $total_marks += $question_bank->mark; @endphp
                                    <tr class="abc">
                                        <td>
                                            <label class="primary_checkbox d-flex  "
                                                   for="question{{ @$question_bank->id}}">
                                                <input type="checkbox" name="questions[]"
                                                       id="question{{ @$question_bank->id}}"
                                                       value="{{ @$question_bank->id}}"
                                                       {{in_array(@$question_bank->id, @$already_assigned)? 'checked': ''}}
                                                       class="common-checkbox question">
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td>{{@$question_bank->questionGroup !=""?@$question_bank->questionGroup->title:""}}</td>
                                        <td>
                                            @php
                                                if (@$question_bank->type == "M") {
                                               echo trans('quiz.Multiple Choice');
                                               } elseif (@$question_bank->type == "S") {
                                               echo trans('quiz.Short Answer');
                                               } elseif (@$question_bank->type == "L") {
                                               echo trans('quiz.Long Answer');
                                               } else {
                                               echo trans('quiz.Fill In The Blanks');
                                               }
                                            @endphp
                                        </td>
                                        <td>{!! $question_bank->question !!}</td>
                                        <td>{{@$question_bank->marks}}</td>
                                        <td>
                                            @if (!empty($question_bank->image))
                                                <img style="max-width: 150px;" src="{{asset($question_bank->image)}}">
                                            @endif
                                        </td>

                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

                <div class="col-lg-4 mt--1">
                    <div class="row">
                        <div class="col-lg-12 no-gutters">
                            <div class="main-title">
                                <h3> {{__('quiz.Quiz Details')}} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="row student-details">
                        <div class="col-lg-12">
                            <div class="student-meta-box mt_25">
                                <div class=" staff-meta-top"></div>
                                <div class="white-box">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="single-meta mt-20">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="value text-left">
                                                            {{__('coupons.Title')}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="name">
                                                            {{$online_exam->title}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="single-meta">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="value text-left">
                                                            {{__('quiz.Passing %')}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="name">
                                                            {{@$online_exam->percentage}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="single-meta">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="value text-left">
                                                            {{__('quiz.Total Marks')}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="name" id="totalMarks">
                                                            {{@$online_exam->total_marks}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="single-meta">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="value text-left">
                                                            {{__('quiz.Total Questions')}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="name" id="totalQuestions">
                                                            {{@$online_exam->total_questions}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade admin-query" id="deleteOnlineExamQuestion">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('lang.delete') @lang('lang.item')</h4>
                    <button type="button" class="close" data-dismiss="modal"><i class="ti-close "></i></button>
                </div>

                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang('lang.are_you_sure_to_delete')</h4>
                    </div>

                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg"
                                data-dismiss="modal">@lang('lang.cancel')</button>
                        {{ Form::open(['route' => 'online-exam-question-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <input type="hidden" name="id" id="online_exam_question_id">
                        <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                        {{ Form::close() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <input type="hidden" name="ques_assign" class="ques_assign"
           value="{{route('online_exam_question_assign_by_ajax')}}">
@endsection
@push('scripts')
    <script src="{{asset('public/backend/js/manage_quiz.js')}}"></script>
    <script>

    </script>
@endpush
