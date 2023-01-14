@extends('backend.master')
@section('mainContent')
    <input type="text" hidden value="{{ @$clas->class_name }}" id="cls">
    <input type="text" hidden value="{{ @$sec->section_name }}" id="sec">
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1> {{__('quiz.Online Quiz')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a href="#">{{__('quiz.Quiz')}}</a>
                    <a href="#"> {{__('quiz.Online Quiz')}} </a>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-12">
            <div class="white-box mb-30">
                {{ Form::open(['class' => 'form-horizontal', 'files' => false,  'method' => 'GET','id' => 'search_student']) }}
                <div class="row">

                    <div class="col-lg-4 mt-30-md md_mb_20">
                        <label class="primary_input_label" for="category_id">{{__('common.Type')}}</label>
                        <select class="primary_select "
                                id="category_id" name="type">
                            <option data-display=" {{__('common.Select')}}" value=""> {{__('common.Type')}}
                            </option>
                            <option value="Course" {{$type=='Course'?'selected':''}}>Course</option>
                            <option value="Quiz" {{$type=='Quiz'?'selected':''}}>Quiz</option>
                        </select>

                    </div>


                    <div class="col-lg-4 mt-100-md md_mb_20">
                        <label class="primary_input_label" for="" style="    height: 30px;"></label>
                        <button type="submit" class="primary-btn small fix-gr-bg">
                            <span class="ti-search pr-2"></span>
                            {{__('quiz.Search')}}
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <section class="mt-20 admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-6 col-md-6">
                    <div class="box_header">
                        <div class="main-title mb_xs_20px">
                            <h3 class="mb-0 mb_xs_20px"> {{__('quiz.Result')}} {{__('common.View')}} </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">

                    <table id="lms_table" class="table Crm_table_active3">
                        <thead>
                        <tr>
                            <th>{{__('common.SL')}} </th>
                            <th> {{__('common.Date')}} </th>
                            <th> {{__('quiz.Student')}} </th>
                            <th> {{__('quiz.Status')}} </th>
                            <th> {{__('quiz.Result')}} </th>
                            <th> {{__('quiz.Duration')}} </th>
                            <th> {{__('quiz.Obtain Marks')}} </th>
                            <th> {{__('common.Action')}} </th>
                        </tr>
                        </thead>
                        <tbody>


                        @foreach($student_details as $key=>$student)
                            <tr>
                                @php
                                    if (($student['status']==1)){
        $totalQus = totalQuizQus($student['quiz_id']);
                                                  $totalAns = count($student['quizDetails']);
                                                  $totalScore = totalQuizMarks($student['quiz_id']);
                                                  $score = 0;
                                                  if ($totalAns != 0) {
                                                      foreach ($student['quizDetails'] as $test) {
                                                           if ($test->status == 1) {
                                                                  $score += $test->mark ?? 1;
                                                              }

                                                      }
                                                  }
    }else{
        $score='--';
    }


                                @endphp
                                <td> {{++$key}} </td>
                                <td> {{$student['date']}} </td>
                                <td> {{$student['name']}} </td>
                                <td> {{$student['status']==1?'Publish':'Pending'}} </td>
                                <td>
                                    @if($student['status']==1)
                                        {{$student['pass']==1?'Pass':'Fail'}}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td> {{$student['duration']}} Min</td>

                                <td> {{@$score}} </td>


                                <td>

                                    <div class="dropdown CRM_dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                id="dropdownMenu2" data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                            {{ __('common.Select') }}
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right"
                                             aria-labelledby="dropdownMenu2">
                                            <a class="dropdown-item edit_brand"
                                               href="{{route('markingScript', [$student['test_id']])}}">
                                                {{__('quiz.View Marking Script')}}
                                            </a>
                                        </div>
                                    </div>
                                </td>


                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>





@endsection
@push('scripts')
    <script src="{{asset('/')}}/Modules/Quiz/Resources/assets/js/quiz.js"></script>
@endpush
