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
    <style>
        .result_sheet_wrapper .quiz_test_body .result_sheet_view .single_result_view:not(:last-child) {
            border-bottom: 1px solid #E9E7F7;
        }

        .result_sheet_wrapper .quiz_test_body .result_sheet_view .single_result_view {
            padding-bottom: 26px;
            margin-bottom: 30px;
        }

        .primary_checkbox {;
            width: auto !important;
        }
    </style>


    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12 mb_20">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-20">

                                    Quiz Marking
                                </h3>
                            </div>
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'quizMarkingStore', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="white-box">
                                <div class="add-visitor">


                                    <div class="result_sheet_wrapper mb_30">
                                        <!-- quiz_test_header  -->
                                        <div class="quiz_test_header">

                                        </div>
                                        <!-- quiz_test_body  -->
                                        <div class="quiz_test_body">
                                            <div class="result_sheet_view">
                                                @php
                                                    $count=1;
                                                @endphp
                                                @if(isset($questions))
                                                    @foreach($questions as $question)

                                                        <div class="single_result_view">
                                                            <div class="row">
                                                                <div class="col-lg-8">
                                                                    <p>{{__('frontend.Question')}}: {{$count}}</p>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    {{__('quiz.Question Marks')}}
                                                                    : {{@$question['question_marks']}}
                                                                </div>
                                                            </div>

                                                            <h2>{!!@$question['qus']!!}</h2>
                                                            <div class="row">
                                                                <div class="col-lg-8">
                                                                    @if ($question['type']=='M')
                                                                        <ul>
                                                                            @if(!empty($question['option']))
                                                                                @foreach($question['option'] as $option)
                                                                                    @if($option['right'])
                                                                                        <li>
                                                                                            <label
                                                                                                class="primary_checkbox d-flex mr-12"
                                                                                                style="padding: 15px">
                                                                                                <input checked=""
                                                                                                       type="checkbox"
                                                                                                       disabled>
                                                                                                <span
                                                                                                    class="checkmark mr_10"></span>
                                                                                                <span
                                                                                                    class="label_name ">{{$option['title']}}</span>
                                                                                            </label>
                                                                                        </li>

                                                                                    @else

                                                                                        @if(isset($option['wrong']) && $option['wrong'])
                                                                                            <li>
                                                                                                <label
                                                                                                    class="primary_checkbox d-flex mr-12 error_ans  "
                                                                                                    style="padding: 15px">
                                                                                                    <input checked=""
                                                                                                           type="checkbox"
                                                                                                           disabled>
                                                                                                    <span
                                                                                                        class="checkmark mr_10"></span>
                                                                                                    <span
                                                                                                        class="label_name ">{{$option['title']}} </span>
                                                                                                </label>
                                                                                            </li>
                                                                                        @else
                                                                                            <li>
                                                                                                <label
                                                                                                    class="primary_checkbox d-flex mr-12"
                                                                                                    style="padding: 15px">
                                                                                                    <input
                                                                                                        type="checkbox"
                                                                                                        disabled>
                                                                                                    <span
                                                                                                        class="checkmark mr_10"></span>
                                                                                                    <span
                                                                                                        class="label_name ">{{$option['title']}}</span>
                                                                                                </label>
                                                                                            </li>
                                                                                        @endif
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                        </ul>
                                                                    @else
                                                                        {!!@$question['answer']!!}
                                                                    @endif

                                                                    <img src="{{asset($question['image'])}}" alt="">

                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="marking_img">
                                                                        @if(isset($question['isSubmit']))
                                                                            @if(isset($question['isWrong']) &&  $question['isWrong'])
                                                                                <img
                                                                                    src="{{asset('public/backend/')}}/img/wrong.png"
                                                                                    alt="">
                                                                            @else
                                                                                <img
                                                                                    src="{{asset('public/backend/')}}/img/correct.png"
                                                                                    alt="">
                                                                            @endif
                                                                            <input type="hidden"
                                                                                   name="mark[{{$question['qus_id']}}]"
                                                                                   value="{{$question['mark']}}">
                                                                        @else

                                                                            <div class="input-effect w-50">

                                                                                <input class="primary_input_field name"
                                                                                       type="text"
                                                                                       max="{{$question['question_marks']}}"
                                                                                       name="mark[{{$question['qus_id']}}]"
                                                                                       value="{{$question['mark']}}"
                                                                                       autocomplete="off" value="">
                                                                                <span class="focus-border"></span>
                                                                            </div>

                                                                        @endif
                                                                        <input type="hidden" name="question[]"
                                                                               value="{{$question['qus_id']}}">
                                                                        <input type="hidden"
                                                                               name="question_marks[{{$question['qus_id']}}]"
                                                                               value="{{$question['question_marks']}}">
                                                                            <input type="hidden"
                                                                                   name="question_type[{{$question['qus_id']}}]"
                                                                                   value="{{$question['type']}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $count++;
                                                        @endphp
                                                    @endforeach
                                                    <input type="hidden" name="quizTestId" value="{{$quizTest->id}}">
{{--                                                    <input type="hidden" name="quiz" value="{{$data['quiz']}}">--}}
{{--                                                    <input type="hidden" name="course" value="{{$data['course']}}">--}}
{{--                                                    <input type="hidden" name="status" value="{{$data['status']}}">--}}
                                                @endif
                                            </div>

                                        </div>
                                    </div>


                                    @php
                                        $tooltip = "";
                                          if (permissionCheck('set-quiz.store')){
                                              $tooltip = "";
                                          }else{
                                              $tooltip = "You have no permission to add";
                                          }
                                    @endphp


                                    @if($quizTest->publish==0)
                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                                        title="{{$tooltip}}">
                                                    <span class="ti-check"></span>
                                                    @if(isset($online_exam))
                                                        {{__('common.Update')}}
                                                    @else
                                                        {{__('common.Save')}}
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" id="url" value="{{Request::url()}}">
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>





@endsection
@push('scripts')
    <script src="{{asset('/')}}/Modules/Quiz/Resources/assets/js/quiz.js"></script>
@endpush
