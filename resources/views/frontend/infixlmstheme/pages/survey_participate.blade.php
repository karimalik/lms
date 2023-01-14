@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('survey.Survey')}} @endsection
@section('css')
    <link href="{{asset('public/backend/css/summernote-bs4.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/frontend/infixlmstheme/css/select2.min.css')}}" rel="stylesheet"/>
@endsection
@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/select2.min.js')}}"></script>
    <script src="{{asset('public/backend/js/summernote-bs4.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.lms_summernote').summernote({
                placeholder: 'Answer',
                tabsize: 2,
                height: 188,
                tooltip: true
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('.select2').css('width', '100%');
        });
    </script>
@endsection

@section('mainContent')
    <style>
        .pb_50 {
            padding-bottom: 50px;
        }
        .cs_modal .modal-body input, .cs_modal .modal-body .nice_Select {
            height: 60px;
            line-height: 50px;
            padding: 0px 22px;
            border: 1px solid #F1F3F5;
            color: #707070;
            font-size: 14px;
            font-weight: 500;
            background-color: #fff;
            width: 100%;
        }
        .modal_1000px {
            max-width: 1000px;
        }
    </style>
    <div class="main_content_iner">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="purchase_history_wrapper pb_50">
                        <div class="row">
                            <div class="col-12">
                                <div class="section__title3 mb_40">
                                    <h3 class="mb-0">{{__('survey.Survey')}} {{__('survey.Participation')}}</h3>
                                    <h4></h4>
                                </div>
                            </div>
                        </div>

                        <div class="white-box " style="border: 1px solid #e8ecf3;; padding: 12px;">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    <h2>
                                        {{$assign->survey->title}}
                                    </h2>
                                    {!! $assign->survey->description !!}
                                </div>
                            </div>
                            <hr>
                            <input type="hidden" id="counter" value="{{$assign->survey->questions->count()}}">
                            <input type="hidden" id="dom_url" value="{{url('/')}}">
                            <form action="{{route('survey.student_survey_participate_store',$assign->id)}}" method="post">
                                <input type="hidden" name="assign_id" value="{{$assign->id}}">
                                @csrf
                                <div class="">
                                    @foreach ($assign->survey->questions as $key => $question)
                                        @php
                                            $submitted_answer=$submitted_answers->where('question_id',$question->id)->first();
                                        @endphp
                                        <div class="single question row">
                                            <div class="col-lg-12">
                                                <h4>{{$key+1}}. {{$question->title}}</h4>
                                                <input type="hidden" name="question[]" value="{{$question->id}}">
                                            </div>
                                            <div class="col-lg-12 d-flex mt-5">
                                                @if ($question->answer_type!='textarea')
                                                    @if ($question->answer_type=='Checkbox')
                                                        @foreach ($question->set->activeAttributes as $attribute)
                                                            <div class="mr-2">
                                                                <label
                                                                    class="primary_bulet_checkbox d-flex">
                                                                    <input class="quizAns"   {{isset($submitted_answers) ? $submitted_answers->where('answer',$attribute->id)->first() ? 'checked':'':''}}
                                                                    name="survey_answer[{{$question->id}}][]"
                                                                           type="checkbox"
                                                                           value="{{$attribute->id}}">

                                                                    <span class="checkmark mr_10"></span>
                                                                    <span class="label_name">{{$attribute->name}} </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @if ($question->answer_type=='radio')
                                                        @foreach ($question->set->activeAttributes as $attribute)
                                                            <div class="mr-2">
                                                                <label
                                                                    class="primary_bulet_checkbox d-flex">
                                                                    <input class="quizAns" {{isset($submitted_answer) ? $submitted_answer->answer == $attribute->id? 'checked':'':''}}
                                                                    name="survey_answer[{{$question->id}}]"
                                                                           type="radio"
                                                                           value="{{$attribute->id}}">

                                                                    <span class="checkmark mr_10"></span>
                                                                    <span class="label_name">{{$attribute->name}} </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @if ($question->answer_type=='dropdown')
                                                        <div class="single_input ">
                                                            <span class="primary_label2">Answer  <span
                                                                    class=""></span> </span>
                                                            <select class="select2 mb-3 wide w-100" name="survey_answer[{{$question->id}}]" {{$errors->first('language') ? 'autofocus' : ''}}>
                                                                <option data-display="Select Answer" value="#">Select Answer</option>
                                                                @foreach ($question->set->activeAttributes as $attribute)
                                                                    <option {{isset($submitted_answer) ? $submitted_answer->answer == $attribute->id? 'selected':'':''}} value="{{$attribute->id}}" >{{$attribute->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                @else
                                                    {{-- @dd($submitted_answers->where('question_id',$question->id)->first()) --}}
                                                    <div class="input-effect mb-20">
                                                        <label class="primary_input_label"> Answer <strong
                                                                class="text-danger"></strong></label>
                                                        <textarea class="textArea lms_summernote "ols="30" rows="10" name="survey_answer[{{$question->id}}]">

                                                    {{$submitted_answers->where('question_id',$question->id)->first() ? $submitted_answers->where('question_id',$question->id)->first()->answer:''}}
                                                </textarea>
                                                        <span class="focus-border textarea"></span>

                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
                                </div>
                                @if ($assign->is_attended==0)
                                    <div class="row">

                                        <div class="col-lg-12 text-center">
                                            <div class="d-flex justify-content-center pt_20">
                                                <button type="submit" class="theme_btn mr_15 m-auto mt-4 text-center"
                                                        data-toggle="tooltip" title=""
                                                        id="save_button_parent">
                                                    <i class="ti-check"></i>
                                                    {{ __('common.Submit') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
