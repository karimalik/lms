<div>
    <div class="quiz__details">
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-xl-10">
                    <div class="row">
                        <div class="col-12">


                            <div class="quiz_score_wrapper mb_30">
                                <!-- quiz_test_header  -->
                                <div class="quiz_test_header">
                                    <h3>{{__('student.Your Exam Score')}}</h3>
                                </div>
                                <!-- quiz_test_body  -->
                                <div class="quiz_test_body">
                                    <h3>{{__('student.Congratulations! You’ve completed')}} {{$course->quiz->title}}</h3>

                                    @if ($quiz->publish==1)
                                        <div class="">
                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="score_view_wrapper">
                                                        <div class="single_score_view">
                                                            <p>{{__('student.Exam Score')}}:</p>
                                                            <ul>
                                                                <li class="mb_15">
                                                                    <label class="primary_checkbox2 d-flex">
                                                                        <input checked="" type="checkbox" disabled>
                                                                        <span class="checkmark mr_10"></span>
                                                                        <span
                                                                            class="label_name">{{$result['totalCorrect']}} {{__('student.Correct Answer')}}</span>
                                                                    </label>
                                                                </li>
                                                                <li>
                                                                    <label class="primary_checkbox2 error_ans d-flex">
                                                                        <input checked="" name="qus" type="checkbox"
                                                                               disabled>
                                                                        <span class="checkmark mr_10"></span>
                                                                        <span
                                                                            class="label_name">{{$result['totalWrong']}} {{__('student.Wrong Answer')}}</span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="single_score_view d-flex">
                                                            <div class="row">
                                                                <div class="col md-2">
                                                                    <p>{{__('frontend.Start')}}</p>
                                                                    <span> {{$result['start_at']}} </span>
                                                                </div>

                                                                <div class="col md-2">
                                                                    <p>{{__('frontend.Finish')}}</p>
                                                                    <span> {{$result['end_at']}}      </span>
                                                                </div>

                                                                <div class="col md-2">
                                                                    <p>{{__('frontend.Duration')}}
                                                                        ({{__('frontend.Minute')}})</p>
                                                                    <h4 class="f_w_700 "> {{$result['duration']}} </h4>
                                                                </div>

                                                                <div class="col md-2">
                                                                    <p>{{__('frontend.Mark')}}</p>
                                                                    <h4 class="f_w_700 "> {{$result['score']}}
                                                                        /{{$result['totalScore']}} </h4>
                                                                </div>

                                                                <div class="col md-2">
                                                                    <p>{{__('frontend.Percentage')}}</p>
                                                                    <h4 class="f_w_700 "> {{$result['mark']}}% </h4>
                                                                </div>

                                                                <div class="col md-2">
                                                                    <p>{{__('frontend.Rating')}}</p>
                                                                    <h4 class="f_w_700 theme_text {{$result['text_color']}}"> {{$result['status']}} </h4>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sumit_skip_btns d-flex align-items-center">
                                            <a href="{{courseDetailsUrl(@$course->id,@$course->type,@$course->slug)}}"
                                               class="theme_btn   mr_20">{{__('student.Done')}}</a>
                                            @if(count($preResult)!=0)
                                                <button type="button"
                                                        class="theme_line_btn  showHistory  mr_20">{{__('frontend.View History')}}</button>
                                            @endif
                                            @if($quiz->quiz->show_result_each_submit==1)
                                                <a href="{{route('quizResultPreview',$quiz->id)}}"
                                                   class=" font_1 font_16 f_w_600 theme_text3 submit_q_btn">{{__('student.See Answer Sheet')}}</a>
                                            @endif

                                        </div>
                                    @else
                                        <span>{{__('quiz.Please wait till completion marking process')}}</span>
                                    @endif

                                    @if(count($preResult)!=0)
                                        <div id="historyDiv" class="pt-5 " style="display:none;">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>{{__('frontend.Date')}}</th>
                                                    <th>{{__('frontend.Mark')}}</th>
                                                    <th>{{__('frontend.Percentage')}}</th>
                                                    <th>{{__('frontend.Rating')}}</th>
                                                    @if($quiz->quiz->show_result_each_submit==1)
                                                        <th>{{__('frontend.Details')}}</th>
                                                    @endif
                                                </tr>
                                                @foreach($preResult as $pre)
                                                    <tr>
                                                        <td>{{$pre['date']}}</td>
                                                        <td>{{$pre['score']}}/{{$pre['totalScore']}}</td>
                                                        <td>{{$pre['mark']}}%</td>
                                                        <td class="{{$pre['text_color']}}">{{$pre['status']}}</td>
                                                        @if($quiz->quiz->show_result_each_submit==1)
                                                            <td>
                                                                <a href="{{route('quizResultPreview',$pre['quiz_test_id'])}}"
                                                                   class=" font_1 font_16 f_w_600 theme_text3 submit_q_btn">{{__('student.See Answer Sheet')}}</a>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


