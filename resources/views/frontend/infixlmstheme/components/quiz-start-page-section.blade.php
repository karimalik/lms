<div>
    @php
        if ($quiz->random_question==1){
        $questions =$quiz->assignRand;
        }else{
        $questions =$quiz->assign;
        }

    @endphp
    <input type="hidden" name="quiz_assign" class="quiz_assign" value="{{count($questions)}}">


    <div class="quiz__details">
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="quiz_questions_wrapper mb_30">
                                <!-- quiz_test_header  -->

                                @if($alreadyJoin!=0 && $quiz->multiple_attend==0)
                                    <div class="quiz_test_header d-flex justify-content-between align-items-center">
                                        <div class="quiz_header_left text-center">
                                            <h3>{{__('frontend.Sorry! You already attempted this quiz')}}</h3>
                                        </div>


                                    </div>
                                @else
                                    <div class="quiz_test_header d-flex justify-content-between align-items-center">
                                        <div class="quiz_header_left">
                                            <h3>{{$quiz->title}}</h3>
                                        </div>

                                        <div class="quiz_header_right">

                                            <span class="question_time">
                                @php
                                    $timer =0;
if (!empty($course->duration)){
    $timer =$course->duration;
}else{
    if(!empty($quiz->question_time_type==1)){
                                            $timer=$quiz->question_time;
                                        }else{
                                           $timer= $quiz->question_time*count($questions);
                                        }
}

                                @endphp

                                <span id="timer">{{$timer}}:00</span> min</span>
                                            <p>{{__('student.Left of this Section')}}</p>
                                        </div>
                                    </div>
                                    <!-- quiz_test_body  -->
                                    <form action="{{route('quizSubmit')}}" method="POST" id="quizForm">
                                        <input type="hidden" name="quizType" value="2">
                                        <input type="hidden" name="courseId" value="{{$course->id}}">
                                        <input type="hidden" name="quizId" value="{{$quiz->id}}">
                                        <input type="hidden" name="question_review" id="question_review"
                                               value="{{$quiz->question_review}}">
                                        <input type="hidden" name="start_at" value="">
                                        <input type="hidden" name="quiz_test_id" value="">
                                        <input type="hidden" name="quiz_start_url" value="{{route('quizTestStart')}}">
                                        <input type="hidden" name="single_quiz_submit_url"
                                               value="{{route('singleQuizSubmit')}}">
                                        @csrf

                                        <div class="quiz_test_body d-none">
                                            <div class="tabControl">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="tab-content" id="pills-tabContent">
                                                            @php
                                                                $count =1;
                                                            @endphp
                                                            @if(isset($questions))
                                                                @foreach($questions as $key=>$assign)
                                                                    <div
                                                                        class="tab-pane fade  {{$key==0?'active show':''}} singleQuestion"
                                                                        data-qus-id="{{$assign->id}}"
                                                                        data-qus-type="{{$assign->questionBank->type}}"
                                                                        id="pills-{{$assign->id}}" role="tabpanel"
                                                                        aria-labelledby="pills-home-tab{{$assign->id}}">
                                                                        <!-- content  -->
                                                                        <div class="question_list_header">


                                                                        </div>
                                                                        <div class="multypol_qustion mb_30">
                                                                            <h4 class="font_18 f_w_700 mb-0"> {!! @$assign->questionBank->question !!}</h4>
                                                                        </div>
                                                                        <input type="hidden" class="question_type"
                                                                               name="type[{{$assign->questionBank->id}}]"
                                                                               value="{{ @$assign->questionBank->type}}">
                                                                        <input type="hidden" class="question_id"
                                                                               name="question[{{$assign->questionBank->id}}]"
                                                                               value="{{ @$assign->questionBank->id}}">

                                                                        @if($assign->questionBank->type=="M")
                                                                            <ul class="quiz_select">
                                                                                @if(isset($assign->questionBank->questionMu))
                                                                                    @foreach(@$assign->questionBank->questionMu as $option)

                                                                                        <li>
                                                                                            <label
                                                                                                class="primary_bulet_checkbox d-flex">
                                                                                                <input class="quizAns"
                                                                                                       name="ans[{{$option->question_bank_id}}][]"
                                                                                                       type="checkbox"
                                                                                                       value="{{$option->id}}">

                                                                                                <span
                                                                                                    class="checkmark mr_10"></span>
                                                                                                <span
                                                                                                    class="label_name">{{$option->title}} </span>
                                                                                            </label>
                                                                                        </li>
                                                                                    @endforeach
                                                                                @endif
                                                                            </ul>
                                                                        @else
                                                                            <div style="margin-bottom: 20px;">
                                                                <textarea class="textArea lms_summernote quizAns"
                                                                          id="editor{{$assign->id}}"
                                                                          cols="30" rows="10"
                                                                          name="ans[{{$assign->questionBank->id}}]"></textarea>
                                                                            </div>
                                                                        @endif
                                                                        @if(!empty($assign->questionBank->image))
                                                                            <div class="ques_thumb mb_50">
                                                                                <img
                                                                                    src="{{asset($assign->questionBank->image)}}"
                                                                                    class="img-fluid" alt="">
                                                                            </div>
                                                                        @endif
                                                                        <div
                                                                            class="sumit_skip_btns d-flex align-items-center mb_50">
                                                                            @if(count($questions)!=$count)
                                                                                <span
                                                                                    class="theme_btn small_btn  mr_20 next"
                                                                                    data-question_id="{{$assign->questionBank->id}}"
                                                                                    data-assign_id="{{$assign->id}}"
                                                                                    data-question_type="{{$assign->questionBank->type}}"
                                                                                    id="next">{{__('student.Continue')}}</span>
                                                                                <span
                                                                                    class=" font_1 font_16 f_w_600 theme_text3 submit_q_btn skip"
                                                                                    id="skip">{{__('student.Skip')}}
                                                                                    {{__('frontend.Question')}}</span>
                                                                            @else
                                                                                <button type="button"
                                                                                        data-question_id="{{$assign->questionBank->id}}"
                                                                                        data-assign_id="{{$assign->id}}"
                                                                                        data-question_type="{{$assign->questionBank->type}}"
                                                                                        class="submitBtn theme_btn small_btn  mr_20">
                                                                                    {{__('student.Submit')}}
                                                                                </button>
                                                                            @endif
                                                                        </div>


                                                                        <!-- content::end  -->
                                                                    </div>
                                                                    @php
                                                                        $count++;
                                                                    @endphp
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6">

                                                        @php
                                                            $count2=1;
                                                        @endphp

                                                        <div class="question_list_header">
                                                            <div class="question_list_top">
                                                                <p>Question <span id="currentNumber">{{$count2}}</span>
                                                                    out
                                                                    of {{count($questions)}}</p>
                                                            </div>
                                                        </div>
                                                        <div class="nav question_number_lists" id="nav-tab"
                                                             role="tablist">
                                                            @if(isset($questions))
                                                                @foreach($questions as $key2=>$assign)
                                                                    <a class="nav-link questionLink link_{{$assign->id}} {{$key2==0?'skip_qus':'pouse_qus'}}"
                                                                       data-toggle="tab" href="#pills-{{$assign->id}}"
                                                                       role="tab" aria-controls="nav-home"
                                                                       data-qus="{{$assign->id}}"
                                                                       aria-selected="true">{{$count2}}</a>
                                                                    @php
                                                                        $count2++;
                                                                    @endphp
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
