@php
    $totalQus =$course->survey->questions->count();
 $assign_info=\Modules\Survey\Entities\SurveyAssign::where('survey_id',$course->survey->id)->where('user_id',auth()->user()->id) ->first();

@endphp
@if($assign_info && $assign_info->survey->title!="")
    <div class="modal fade " id="assignSubmit"
         tabindex="-1"
         role="dialog" aria-labelledby="assignModalLabel"
         aria-hidden="true">
        <div class="modal-dialog   modal-dialog-centered " role="document" style="max-width: 70%;">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="assignModalLabel">
                        {{$course->survey->title}}
                    </h5>
                    {!! $course->survey->description !!}
                </div>
                <form action="{{route('survey.student_survey_participate_store',$assign_info->id)}}" method="post">
                    <input type="hidden" name="assign_id" value="{{$assign_info->id}}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="quiz_questions_wrapper mb_30">

                                    <!-- quiz_test_body  -->
                                    <div class="quiz_test_body">
                                        <div class="tabControl">
                                            <!-- nav-pills  -->
                                            <ul class="nav nav-pills nav-fill d-none" id="pills-tab" role="tablist">
                                                @foreach ($course->survey->questions as $key => $question)
                                                    <li class="nav-item">
                                                        <a class="nav-link {{$key==0?'active':''}}" id="pills-home-tab"
                                                           data-toggle="pill"
                                                           href="#pills-{{$key}}" role="tab">Tab1</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <!-- tab-content  -->
                                            <div class="tab-content" id="pills-tabContent">
                                                @foreach ($course->survey->questions as $key => $question)
                                                    @php
                                                        //   $submitted_answer=$submitted_answers->where('question_id',$question->id)->first();
                                                    @endphp
                                                    <div class="tab-pane fade {{$key==0?'show active':''}}"
                                                         id="pills-{{$key}}" role="tabpanel"
                                                         aria-labelledby="pills-home-tab">
                                                        <!-- content  -->
                                                        <div class="question_list_header">
                                                            <div class="question_list_top">
                                                                @php
                                                                    $currentSerial =1+$key;
                                                                @endphp
                                                                <p>{{__('common.Question')}} {{$currentSerial}} {{__('common.out of')}} {{$totalQus}}</p>
                                                                <div class="arrow_controller">
                                                                    <span class="btnPrevious"> <i
                                                                            class="ti-angle-left"></i> </span>
                                                                    <span class="btnNext"> <i
                                                                            class="ti-angle-right"></i> </span>
                                                                </div>
                                                            </div>
                                                            <div class="question_list_counters">
                                                                @for($i=0;$i<$totalQus;$i++)
                                                                    @php
                                                                        $serial =1+$i;
                                                                    @endphp
                                                                    <span
                                                                        class="{{$key==$i?'skip_qus':''}}">{{$serial}}</span>

                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <div class="multypol_qustion mb_30">
                                                            <h4 class="font_18 f_w_700 mb-0">
                                                                {{$question->title}}</h4>
                                                            <input type="hidden" name="question[]"
                                                                   value="{{$question->id}}">
                                                        </div>

                                                        <div class="quiz_select">
                                                            @if ($question->answer_type!='textarea')
                                                                @if ($question->answer_type=='Checkbox')
                                                                    @foreach ($question->set->activeAttributes as $attribute)
                                                                        <div class="mr-2">
                                                                            <label
                                                                                class="primary_bulet_checkbox d-flex">
                                                                                <input class="quizAns"
                                                                                       {{isset($submitted_answer) ? $submitted_answer->answer == $attribute->id? 'checked':'':''}}
                                                                                       name="survey_answer[{{$question->id}}]"
                                                                                       type="checkbox"
                                                                                       value="{{$attribute->id}}">

                                                                                <span class="checkmark mr_10"></span>
                                                                                <span
                                                                                    class="label_name">{{$attribute->name}} </span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                                @if ($question->answer_type=='radio')
                                                                    @foreach ($question->set->activeAttributes as $attribute)
                                                                        <div class="mr-2">
                                                                            <label
                                                                                class="primary_bulet_checkbox d-flex">
                                                                                <input class="quizAns"
                                                                                       {{isset($submitted_answer) ? $submitted_answer->answer == $attribute->id? 'checked':'':''}}
                                                                                       name="survey_answer[{{$question->id}}]"
                                                                                       type="radio"
                                                                                       value="{{$attribute->id}}">

                                                                                <span class="checkmark mr_10"></span>
                                                                                <span
                                                                                    class="label_name">{{$attribute->name}} </span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                                @if ($question->answer_type=='dropdown')
                                                                    <div class="single_input ">
                                                            <span class="primary_label2">{{__('common.Answer')}}  <span
                                                                    class=""></span> </span>
                                                                        <select class="select2 mb-3 wide w-100"
                                                                                name="survey_answer[{{$question->id}}]" {{$errors->first('language') ? 'autofocus' : ''}}>
                                                                            <option data-display="Select Answer"
                                                                                    value="#">{{__('common.Select')}} {{__('common.Answer')}}</option>
                                                                            @foreach ($question->set->activeAttributes as $attribute)
                                                                                <option
                                                                                    {{isset($submitted_answer) ? $submitted_answer->answer == $attribute->id? 'selected':'':''}} value="{{$attribute->id}}">{{$attribute->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                {{-- @dd($submitted_answers->where('question_id',$question->id)->first()) --}}
                                                                <div class="input-effect mb-20">
                                                                    <label
                                                                        class="primary_input_label"> {{__('common.Answer')}}
                                                                        <strong
                                                                            class="text-danger"></strong></label>
                                                                    <textarea class="textArea lms_summernote " ols="30"
                                                                              rows="10"
                                                                              name="survey_answer[{{$question->id}}]">

{{--                                                    {{$submitted_answers->where('question_id',$question->id)->first() ? $submitted_answers->where('question_id',$question->id)->first()->answer:''}}--}}
                                                </textarea>
                                                                    <span class="focus-border textarea"></span>

                                                                </div>
                                                            @endif
                                                        </div>


                                                        @if($totalQus!=$currentSerial)
                                                            <div class="sumit_skip_btns d-flex align-items-center">
                                                                <a href="#"
                                                                   class="theme_btn small_btn  mr_20 btnNext">{{__('common.Continue')}}</a>
                                                                <a href="#"
                                                                   class=" font_1 font_16 f_w_600 theme_text3 submit_q_btn btnNext">
                                                                    {{__('common.Skip')}} {{__('common.Question')}}</a>
                                                            </div>
                                                        @else

                                                            <div class="sumit_skip_btns d-flex align-items-center">
                                                                <button type="submit"
                                                                        class="theme_btn small_btn  mr_20 btnNext">{{__('common.Submit')}}</button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </form>
            </div>
        </div>
    </div>


    <script>
        $('.btnNext').click(function () {
            $('.nav-pills .active').parent().next('li').find('a').trigger('click');
        });

        $('.btnPrevious').click(function () {
            $('.nav-pills .active').parent().prev('li').find('a').trigger('click');
        });


    </script>
@endif
