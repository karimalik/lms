@php
    $user = Auth::user();
    if ($user->role_id == 1) {
        $groups = Modules\Quiz\Entities\QuestionGroup::where('active_status', 1)->latest()->get();
    } else {
        $groups = Modules\Quiz\Entities\QuestionGroup::where('active_status', 1)->where('user_id', $user->id)->latest()->get();
    }
@endphp
@if(isset($bank))

{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => array('question-bank-update.course',$bank->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'question_bank']) }}

@else
@if (permissionCheck('question-bank.store'))

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'question-bank.course',
    'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'question_bank']) }}

@endif
@endif

<input type="hidden" id="url" value="{{url('/')}}">
<input type="hidden" name="course_id" value="{{@$course->id}}">
<input type="hidden" name="category" value="{{@$course->category_id}}">
<input type="hidden" name="question_type" value="M">
<input type="hidden" id="quiz_id_inside{{$chapter->id}}" name="quize_id" value="">
@if (isset($course->subcategory_id))
    <input type="hidden" name="sub_category" value="{{@$course->subcategory_id}}">
@endif
<div class="section-white-box">
<div class="add-visitor">
    <input type="hidden" name="chapterId" value="{{@$chapter->id}}">
    <div class="row">
        <div class="col-lg-12">

                <div class="row mt-25">
                    <div class="col-lg-12">
                        <div class="input-effect">
                            <label> {{__('quiz.Question')}} *</label>
                            <textarea {{ $errors->has('question') ? ' autofocus' : '' }}
                                      class="primary_input_field name{{ $errors->has('question') ? ' is-invalid' : '' }}"
                                      rows="4"
                                      name="question">{{isset($bank)? strip_tags($bank->question):(old('question')!=''?(old('question')):'')}}</textarea>
                            <span class="focus-border textarea"></span>
                            @if ($errors->has('question'))
                                <span
                                    class="error text-danger"><strong>{{ $errors->first('question') }}</strong></span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row mt-25">
                    <div class="col-lg-12">
                        <div class="input-effect">
                            <label> {{__('quiz.Marks')}} *</label>
                            <input {{ $errors->has('marks') ? ' autofocus' : '' }}
                                   class="primary_input_field name{{ $errors->has('marks') ? ' is-invalid' : '' }}"
                                   type="number" name="marks"
                                   value="{{isset($bank)? $bank->marks:(old('marks')!=''?(old('marks')):'')}}">
                            <span class="focus-border"></span>
                            @if ($errors->has('marks'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('marks') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="multiple-choice">
                    <div class="row  mt-25">
                        <div class="col-lg-8">
                            <div class="input-effect">
                                <label> {{__('quiz.Number Of Options')}}*</label>
                                <input {{ $errors->has('number_of_option') ? ' autofocus' : '' }}
                                        class="primary_input_field name{{ $errors->has('number_of_option') ? ' is-invalid' : '' }}"
                                        type="number" name="number_of_option" autocomplete="off"
                                        id="number_of_option{{$chapter->id}}"
                                        value="{{isset($bank)? $bank->number_of_option: ''}}">
                                <span class="focus-border"></span>
                                @if ($errors->has('number_of_option'))
                                    <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('number_of_option') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 mt-40">
                            <button type="button" data-chapter_id="{{$chapter->id}}" class="primary-btn small fix-gr-bg"
                                    id="create-option">{{__('quiz.Create')}} </button>
                        </div>
                    </div>
                </div>
                <div class="multiple-options"  id="multiple-options{{$chapter->id}}">
               @php
                   $i=0;
                   $multiple_options = [];

                   if(isset($bank)){
                       if($bank->type == "M"){
                           $multiple_options = $bank->questionMu;
                       }
                   }
               @endphp
               @foreach($multiple_options as $multiple_option)

                   @php $i++; @endphp
                   <div class='row  mt-25'>
                       <div class='col-lg-10'>
                           <div class='input-effect'>
                               <label> {{__('quiz.Option')}} {{$i}}</label>
                               <input class='primary_input_field name' type='text'
                                      name='option[]' autocomplete='off' required
                                      value="{{$multiple_option->title}}">
                               <span class='focus-border'></span>
                           </div>
                       </div>
                       <div class='col-lg-2 mt-40'>
                           <label class="primary_checkbox d-flex mr-12 "
                                  for="option_check_{{$i}}" {{__('quiz.Yes')}}>
                               <input type="checkbox" @if ($multiple_option->status==1) checked
                                      @endif id="option_check_{{$i}}"
                                      name="option_check_{{$i}}" value="1">
                               <span class="checkmark"></span>
                           </label>
                       </div>
                   </div>
               @endforeach
           </div>
                {{-- End New Create --}}
            
      
            

        </div>
    </div>

    <div class="row mt-40">
        <div class="col-lg-12 text-center">
            <button  data-chapter_id="{{$chapter->id}}" type="button" class="primary-btn fix-gr-bg close_edit_question_section"
                    data-toggle="tooltip">
                {{__('common.Close')}}
            </button>
            <button type="submit" class="primary-btn fix-gr-bg"
                    data-toggle="tooltip">
                <span class="ti-check"></span>
                {{__('common.Save')}}
            </button>
        </div>
    </div>
</div>
</div>
{{ Form::close() }}

@push('js')
    <script>
        $('.close_edit_question_section').click(function(){
            var chapter_id=$(this).data('chapter_id');
            $('#edit_question'+chapter_id).hide();
        })
    </script>
@endpush