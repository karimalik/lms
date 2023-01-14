<link rel="stylesheet" href="{{asset('Modules/CourseSetting/Resources/assets/css/style.css')}}">
<style type="text/css">
    .erp_role_permission_area {
        display: block !important;
    }

    .single_permission {
        margin-bottom: 0px;
    }

    .erp_role_permission_area .single_permission .permission_body > ul > li ul {
        display: grid;
        margin-left: 8px;
        grid-template-columns: repeat(3, 1fr);
    }

    .erp_role_permission_area .single_permission .permission_body > ul > li ul li {
        margin-right: 15px;

    }

    .mesonary_role_header {
        /* column-count: 2; */
        column-gap: 30px;
    }

    .single_role_blocks {
        display: inline-block;
        background: #fff;
        box-sizing: border-box;
        width: 100%;
        margin: 0 0 5px;
    }

    .erp_role_permission_area .single_permission .permission_body > ul > li {
        padding: 15px 25px 12px 25px;
    }

    .erp_role_permission_area .single_permission .permission_header {
        padding: 20px 25px 11px 25px;
        position: relative;
    }

    @media (min-width: 320px) and (max-width: 1199.98px) {
        .mesonary_role_header {
            column-count: 1;
            column-gap: 30px;
        }
    }

    @media (min-width: 320px) and (max-width: 767.98px) {
        .erp_role_permission_area .single_permission .permission_body > ul > li ul {
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 10px
        }
    }

    .permission_header {
        position: relative;
    }

    .arrow::after {
        position: absolute;
        content: "\e622";
        top: 50%;
        right: 12px;
        height: auto;
        font-family: 'themify';
        color: #fff;
        font-size: 18px;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        right: 22px;
    }

    .arrow.collapsed::after {
        content: "\e61a";
        color: #fff;
        font-size: 18px;
    }

    .erp_role_permission_area .single_permission .permission_header div {
        position: relative;
        top: -5px;
        position: relative;
        z-index: 999;
    }

    .erp_role_permission_area .single_permission .permission_header div.arrow {
        position: absolute;
        width: 100%;
        z-index: 0;
        left: 0;
        bottom: 0;
        top: 0;
        right: 0;
    }

    .erp_role_permission_area .single_permission .permission_header div.arrow i {
        color: #FFF;
        font-size: 20px;
    }

    .rtl .arrow::after {
        right: auto;
        left: 22px;
    }

    .rtl .common-radio:empty ~ label {
        float: right !important;
    }

    .rtl .erp_role_permission_area .single_permission .permission_body > ul > li ul li {
        margin-right: 0;
    }

    .rtl .erp_role_permission_area .single_permission .permission_body > ul > li ul label {

        white-space: nowrap;
    }

    .capter_body {
        border: 1px solid #9f35ee;
    }

    .erp_role_permission_area {
        margin-top: 30px;
    }

    .permission_body {
        padding: 15px;
    }

    .quiz_inside_icon i {
        transform: rotate(180deg);
        display: inline-block;
    }

    .quiz_inside_icon.collapsed i {
        transform: rotate(0);
        display: inline-block;
    }

    a.mr-10.chapter_icon {
        color: #fff;
    }

</style>
{{-- <div class="role_permission_wrap">
    <div class="permission_title">
        <h4>Assign Permission ({{@$role->name}})</h4>
    </div>
</div> --}}
@if (isset($editChapter))
    <div class="row" id="edit_chapter_section">
        <div class="col-lg-1"></div>
        <div class="col-lg-10 section_content">
            @include('coursesetting::parts_of_course_details.chapter_section')
        </div>
        <div class="col-lg-1"></div>

    </div>
@endif

<div class="erp_role_permission_area mt 30">
    <!-- single_permission  -->
    <div class="mesonary_role_header nastable" id="sortable">
        @foreach($chapters as $key => $chapter)
            <div class="modal fade admin-query"
                 id="deleteChapter{{$chapter->id}}">
                <div
                    class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{__('common.Delete')}}  {{__('courses.Chapter')}}</h4>
                            <button type="button"
                                    class="close"
                                    data-dismiss="modal">
                                <i
                                    class="ti-close "></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="text-center">
                                <h4> {{__('common.Are you sure to delete ?')}}</h4>
                            </div>

                            <div
                                class="mt-40 d-flex justify-content-between">
                                <button type="button"
                                        class="primary-btn tr-bg"
                                        data-dismiss="modal">{{__('common.Cancel')}}</button>
                                <form
                                    action="{{route('deleteLesson')}}"
                                    method="post">

                                    <a href="{{url('admin/course/delete-chapter/'.$chapter->id.'/'.$course->id)}}"
                                       class="primary-btn fix-gr-bg"
                                       type="submit">{{__('common.Delete')}}</a>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="single_role_blocks parent" data-id="{{$chapter->id}}">
                <div class="single_permission" id="chapter_id{{$key}}">
                    <div class="permission_header d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ti-move text-white"></i>
                            <label for="Main_Module_1" class="pl-10">{{@$chapter->name}}</label>
                        </div>
                        {{-- <div class="arrow collapsed"  data-toggle="collapse" data-target="#Rolechapter_id{{$key}}"  aria-expanded="true"> --}}
                        <div class="mr-20 mt-1">
                            <a class="mr-20 chapter_icon"
                               href="{{url('admin/course/course-chapter-show/'.$course->id.'/'.$chapter->id)}}"> <i
                                    class="ti-pencil-alt"></i> </a>
                            {{--                       <a class="mr-20 chapter_icon"  href="{{url('admin/course/delete-chapter/'.$chapter->id.'/'.$course->id)}}" > <i class="ti-trash"></i> </a>--}}

                            <a href="#" data-toggle="modal" data-target="#deleteChapter{{@$chapter->id}}"
                               class="mr-20 chapter_icon">
                                <i class="ti-trash"></i></a>

                        </div>


                        @if (isset($data['chapter_id']))
                            @if($data['chapter_id']==$chapter->id)
                                <div class="arrow" data-toggle="collapse" data-target="#Rolechapter_id{{$key}}"
                                     aria-expanded="true">
                                    @else
                                        <div class="arrow collapsed" data-toggle="collapse"
                                             data-target="#Rolechapter_id{{$key}}">
                                            @endif
                                            @else
                                                <div class="arrow collapsed" data-toggle="collapse"
                                                     data-target="#Rolechapter_id{{$key}}">
                                                    @endif
                                                </div>
                                        </div>
                                        <div id="Rolechapter_id{{$key}}"
                                             class="collapse capter_body @if(isset($data['chapter_id']) && $data['chapter_id']==$chapter->id) show @endif">
                                            {{-- start option head --}}

                                            <div class="row d-flex mt-30 pl-20">
                                                <div class="col-lg-2">
                                                    <button class="primary-btn icon-only mr-10 fix-gr-bg add_option_box"
                                                            data-chapter="{{$key}}" id="add_option_box{{$key}}"><i
                                                            class="ti-plus"></i></button>
                                                    <button
                                                        class="primary-btn icon-only mr-10 fix-gr-bg minus_option_box"
                                                        data-chapter="{{$key}}" id="minus_option_box{{$key}}"
                                                        style="display: none">X
                                                    </button>
                                                </div>
                                                <div class="col-lg-10">
                                                    <div class="lms_option_box d-flex">
                                                        <div class="pt-20 pb-30 lms_option_list_inside"
                                                             id="lms_option_list{{$key}}" style="display: none">
                                                            <div class="add-item-forms--inline-menu--1OTdc">

                                                                <button data-purpose="add-lesson-btn"
                                                                        aria-label="Add Lesson" type="button"
                                                                        data-chapter="{{$key}}"
                                                                        id="show_lesson_section_inside"
                                                                        class="ellipsis btn btn-tertiary btn-block show_lesson_section_inside">
                                                                    <i class="ti-plus"></i>
                                                                    {{__('courses.Lesson')}}
                                                                </button>
                                                                @if (isModuleActive('Assignment'))

                                                                    <button data-purpose="add-chapter-btn"
                                                                            aria-label="Add Assignment" type="button"
                                                                            data-chapter="{{$key}}"
                                                                            id="show_assignment_section_inside"
                                                                            class="ellipsis btn btn-tertiary btn-block show_assignment_section_inside">
                                                                        <i class="ti-plus"></i> {{__('assignment.Assignment')}}
                                                                    </button>
                                                                @endif
                                                                <button data-purpose="add-quiz-btn"
                                                                        aria-label="Add Quiz" type="button"
                                                                        data-chapter="{{$key}}"
                                                                        id="show_quiz_section_inside"
                                                                        class="ellipsis btn btn-tertiary btn-block show_quiz_section_inside">
                                                                    <i class="ti-plus"></i> {{__('quiz.Quiz')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="row" id="lesson_section{{$key}}" style="display: none">
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-10 section_content">
                                                    @include('coursesetting::parts_of_course_details.lesson_section_inside')
                                                </div>
                                                <div class="col-lg-1"></div>

                                            </div>
                                            <div class="row" id="assignment_section{{$key}}" style="display: none">
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-10 section_content">
                                                    @include('coursesetting::parts_of_course_details.add_assignment_section_inside')
                                                </div>
                                                <div class="col-lg-1"></div>

                                            </div>
                                            <div class="row" id="quiz_section{{$key}}" style="display: none">
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-10 section_content">
                                                    @include('coursesetting::parts_of_course_details.quiz_section_inside')
                                                </div>
                                                <div class="col-lg-1"></div>

                                            </div>


                                            @if (isset($data['edit_chapter_id']) && $data['edit_chapter_id']==$chapter->id)

                                                <div class="row" id="edit_question{{$chapter->id}}">
                                                    <div class="col-lg-1"></div>
                                                    <div class="col-lg-10 section_content">
                                                        @include('coursesetting::parts_of_course_details.edit_question_section_inside')
                                                    </div>
                                                    <div class="col-lg-1"></div>

                                                </div>
                                            @endif

                                            <div class="row" style="display: none">
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-10 section_content">

                                                </div>
                                                <div class="col-lg-1"></div>

                                            </div>
                                            {{-- END ITEM SECTION --}}
                                            <div class="permission_body nastable2" data-chapter="{{$chapter->id}}">
                                                @foreach ($chapter->lessons as $key => $lesson)
                                                    @if ($key==0)
                                                        <div class="row mb-40"
                                                             id="add_question_section_inside{{$chapter->id}}"
                                                             style="display: none">
                                                            <div class="col-lg-1"></div>
                                                            <div class="col-lg-10 section_content">
                                                                @include('coursesetting::parts_of_course_details.question_section_inside')
                                                            </div>
                                                            <div class="col-lg-1"></div>

                                                        </div>
                                                    @endif
                                                    @if (isset($data['edit_lesson_id']) && $data['edit_lesson_id']==$lesson->id)
                                                        <div class="row" id="edit_question{{$lesson->id}}">
                                                            <div class="col-lg-1"></div>
                                                            <div class="col-lg-10 section_content">
                                                                @if ($editLesson->is_quiz==1)
                                                                    @include('coursesetting::parts_of_course_details.edit_quiz_section_inside')
                                                                @else
                                                                    @php
                                                                        $editSection=true;
                                                                    @endphp
                                                                    @include('coursesetting::parts_of_course_details.lesson_section_inside',compact('editSection'))
                                                                @endif
                                                            </div>
                                                            <div class="col-lg-1"></div>

                                                        </div>
                                                    @endif
                                                    @if (isset($data['edit_assignment_id']) && $data['edit_assignment_id']==$lesson->id)
                                                        <div class="row" id="edit_question{{$lesson->id}}">
                                                            <div class="col-lg-1"></div>
                                                            <div class="col-lg-10 section_content">
                                                                @include('coursesetting::parts_of_course_details.assignment_section_inside')
                                                            </div>
                                                            <div class="col-lg-1"></div>

                                                        </div>
                                                    @endif
                                                    @push('js')
                                                        <script>
                                                            // $(".child").sortable();
                                                        </script>
                                                    @endpush
                                                    <div class="child" data-id="{{$lesson->id}}">
                                                        <div
                                                            class="single_capter_list d-flex align-items-center justify-content-between flex-wrap mt-10">
                                                            @if ($lesson->is_quiz==1)
                                                                @foreach ($lesson->quiz as $quiz_key=> $quiz)
                                                                    <span class="flex-fill"> <i
                                                                            class="ti-move"></i>   <span
                                                                            class="serial">{{$key+1}}</span>. {{@$quiz->title}}</span>
                                                                    <a class="primary-btn icon-only mr-10 fix-gr-bg quiz_inside_icon collapsed"
                                                                       data-toggle="collapse"
                                                                       href="#collapseExample{{$lesson->id}}{{$quiz->id}}"
                                                                       role="button" aria-expanded="false"
                                                                       aria-controls="collapseExample">
                                                                        <i class="ti-arrow-down"></i>
                                                                    </a>
                                                                    <div class="dropdown CRM_dropdown">
                                                                        <button
                                                                            class="btn btn-secondary dropdown-toggle"
                                                                            type="button" id="dropdownMenu2"
                                                                            data-toggle="dropdown"
                                                                            aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            {{__('common.Action')}}
                                                                        </button>
                                                                        <div
                                                                            class="dropdown-menu dropdown-menu-right">
                                                                            {{-- <a target="_blank"
                                                                               href="{{$lesson->is_quiz==0?route('fullScreenView',[$course->id,$lesson->id]):route('quizStart',[$course->id,$lesson->quiz_id,$lesson->lessonQuiz->title])}}"
                                                                               class="dropdown-item">{{__('common.View')}}</a> --}}
                                                                            <a target="_blank"
                                                                               href="{{route('fullScreenView',[$course->id,$lesson->id])}}"
                                                                               class="dropdown-item">{{__('common.View')}}</a>
                                                                            <a href="{{url('admin/course/course-lesson-show/'.$course->id.'/'.$chapter->id.'/'.$lesson->id)}}"
                                                                               class="dropdown-item">{{__('common.Edit')}}</a>
                                                                            @if ($lesson->is_quiz==1)
                                                                                <a class="dropdown-item add_question"
                                                                                   data-lesson_id="{{$quiz->id}}"
                                                                                   data-chapter_id="{{$chapter->id}}"
                                                                                   href="#">Add Question</a>
                                                                            @endif
                                                                            <a href="#" data-toggle="modal"
                                                                               data-target="#deleteLesson{{@$lesson->id}}"
                                                                               class="dropdown-item"
                                                                               type="button">{{__('common.Delete')}}</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="collapse w-100 mt-10"
                                                                         id="collapseExample{{$lesson->id}}{{$quiz->id}}">
                                                                        <div class="card card-body">
                                                                            @if ($quiz->assign->count()>0)

                                                                                <table>
                                                                                    @foreach ($quiz->assign as $question_key => $assign)
                                                                                        @php
                                                                                            if($assign->questionBank->type!='M'){
                                                                                                continue;
                                                                                            }
                                                                                        @endphp
                                                                                        <tr>
                                                                                            <td>{{$question_key+1}}</td>
                                                                                            <td>{!! $assign->questionBank->question !!}</td>
                                                                                            <td>
                                                                                                <div class="btn-group">

                                                                                                    <a href="{{url('admin/course/course-question-show/'.$assign->questionBank->id.'/'.$course->id.'/'.$chapter->id.'/'.$lesson->id)}}"><i
                                                                                                            class="ti-pencil-alt"></i>
                                                                                                    </a>
                                                                                                    <a href="{{route('CourseQuestionDelete',['quiz_id'=>$quiz->id,'question_id'=>$assign->questionBank->id])}}   "
                                                                                                       class="ml-10"
                                                                                                       title="Delete"><i
                                                                                                            class="ti-trash"></i>
                                                                                                    </a>
                                                                                                    {{-- <a href="{{url('quiz/online-exam-question-unassign/'.$course->id.'/'.$quiz->id.'/'.$assign->questionBank->id)}}" class="ml-10" title="Question Unassigned"><i class="ti-unlink"></i> </a> --}}
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </table>
                                                                            @else
                                                                                <span>No Assigned Question</span>
                                                                            @endif
                                                                            {{-- <ul>
                                                                                @foreach ($quiz->assign as $question_key => $assign)
                                                                                    <li>{{$question_key+1}}. {{$assign->questionBank->question}}</li>
                                                                                @endforeach
                                                                            </ul> --}}
                                                                        </div>
                                                                    </div>


                                                                @endforeach
                                                            @elseif($lesson->is_assignment==1)

                                                                @foreach ($lesson->assignment as $assignment_key=> $assignment)
                                                                    <span class="flex-fill"> <i
                                                                            class="ti-move"></i>   <span
                                                                            class="serial">{{$key+1}}</span>. {{@$assignment->title}}</span>

                                                                    <div class="dropdown CRM_dropdown">
                                                                        <button
                                                                            class="btn btn-secondary dropdown-toggle"
                                                                            type="button" id="dropdownMenu2"
                                                                            data-toggle="dropdown"
                                                                            aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            {{__('common.Action')}}
                                                                        </button>
                                                                        <div
                                                                            class="dropdown-menu dropdown-menu-right">
                                                                            <a target="_blank"
                                                                               href="{{route('fullScreenView',[$course->id,$lesson->id])}}"
                                                                               class="dropdown-item">{{__('common.View')}}</a>
                                                                            @if (isModuleActive('Assignment'))
                                                                                <a href="{{url('admin/course/course-assignment-show/'.$course->id.'/'.$chapter->id.'/'.$lesson->id)}}"
                                                                                   class="dropdown-item">{{__('common.Edit')}}</a>
                                                                            @endif
                                                                            <a href="#" data-toggle="modal"
                                                                               data-target="#deleteLesson{{@$lesson->id}}"
                                                                               class="dropdown-item"
                                                                               type="button">{{__('common.Delete')}}</a>
                                                                        </div>
                                                                    </div>



                                                                @endforeach
                                                            @else

                                                                <span> <i
                                                                        class="ti-move"></i>  <span
                                                                        class="serial">{{$key+1}}</span>. {{$lesson['name']}} [{{MinuteFormat($lesson['duration'])}}] [{{$lesson->is_lock==0?'unlock':'Lock'}}]</span>

                                                                <div class="dropdown CRM_dropdown">
                                                                    <button
                                                                        class="btn btn-secondary dropdown-toggle"
                                                                        type="button" id="dropdownMenu2"
                                                                        data-toggle="dropdown"
                                                                        aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        {{__('common.Action')}}
                                                                    </button>
                                                                    <div
                                                                        class="dropdown-menu dropdown-menu-right">
                                                                        <a target="_blank"
                                                                           href="{{$lesson->is_quiz==0?route('fullScreenView',[$course->id,$lesson->id]):route('quizStart',[$course->id,$lesson->quiz_id,$lesson->lessonQuiz->title])}}"
                                                                           class="dropdown-item">{{__('common.View')}}</a>
                                                                        <a href="{{url('admin/course/course-lesson-show/'.$course->id.'/'.$chapter->id.'/'.$lesson->id)}}"
                                                                           class="dropdown-item">{{__('common.Edit')}}</a>
                                                                        <a href="#" data-toggle="modal"
                                                                           data-target="#deleteLesson{{@$lesson->id}}"
                                                                           class="dropdown-item"
                                                                           type="button">{{__('common.Delete')}}</a>
                                                                    </div>
                                                                </div>
                                                            @endif


                                                        </div>


                                                        <div class="modal fade admin-query"
                                                             id="deleteLesson{{$lesson->id}}">
                                                            <div
                                                                class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">{{__('common.Delete')}}  {{__('courses.Lesson')}}</h4>
                                                                        <button type="button"
                                                                                class="close"
                                                                                data-dismiss="modal">
                                                                            <i
                                                                                class="ti-close "></i>
                                                                        </button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="text-center">
                                                                            <h4> {{__('common.Are you sure to delete ?')}}</h4>
                                                                        </div>

                                                                        <div
                                                                            class="mt-40 d-flex justify-content-between">
                                                                            <button type="button"
                                                                                    class="primary-btn tr-bg"
                                                                                    data-dismiss="modal">{{__('common.Cancel')}}</button>
                                                                            <form
                                                                                action="{{route('deleteLesson')}}"
                                                                                method="post">
                                                                                @csrf
                                                                                <input type="hidden"
                                                                                       name="id"
                                                                                       value="{{$lesson->id}}">
                                                                                <button
                                                                                    class="primary-btn fix-gr-bg"
                                                                                    type="submit">{{__('common.Delete')}}</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endforeach
                                            </div>
                                        </div>
                                </div>
                    </div>
                    @endforeach
                </div>

            </div>


            @push('js')
                <script>

                    $('.add_question').click(function (e) {
                        e.preventDefault();
                        var lesson_id = $(this).data('lesson_id');
                        var chapter_id = $(this).data('chapter_id');
                        $('#add_question_section_inside' + chapter_id).show();
                        $('#quiz_id_inside' + chapter_id).val(lesson_id);
                        console.log(lesson_id);
                        console.log(chapter_id);
                    });
                    $('.close_question_section').click(function () {
                        var chapter_id = $(this).data('chapter_id');
                        $('#add_question_section_inside' + chapter_id).hide();
                    })

                    var lms_option_list = $('.lms_option_list');
                    var minus_option_box = $('#minus_option_box');
                    var add_option_box = $('#add_option_box');
                    var chapter_section = $('#chapter_section');
                    var lesson_section = $('#lesson_section');
                    var quiz_section = $('#quiz_section');

                    $('.add_option_box').click(function () {
                        var chapter_id = $(this).data('chapter');
                        $('#lms_option_list' + chapter_id).show();
                        $('#minus_option_box' + chapter_id).show();
                        $('#add_option_box' + chapter_id).hide();
                    });

                    $('.minus_option_box').click(function () {
                        var chapter_id = $(this).data('chapter');
                        $('#chapter_section' + chapter_id).hide();
                        $('#lesson_section' + chapter_id).hide();
                        $('#assignment_section' + chapter_id).hide();
                        $('#quiz_section' + chapter_id).hide();
                        $('#lms_option_list' + chapter_id).hide();
                        $('#add_option_box' + chapter_id).show();
                        $('#minus_option_box' + chapter_id).hide();
                    });

                    $(document).ready(function () {
                        $('#lms_option_list').hide();
                    })
                    $('#add_option_box').click(function () {
                        lms_option_list.show();
                        minus_option_box.show();
                        add_option_box.hide();
                    })
                    $('#minus_option_box').click(function () {
                        lms_option_list.hide();
                        minus_option_box.hide();
                        lesson_section.hide();
                        quiz_section.hide();
                        chapter_section.hide();
                        add_option_box.show();
                    })
                    $('#show_chapter_section').click(function () {
                        lms_option_list.hide();
                        lesson_section.hide();
                        quiz_section.hide();
                        chapter_section.show();
                    })
                    $('#show_lesson_section').click(function () {
                        lms_option_list.hide();
                        lesson_section.show();
                        quiz_section.hide();
                        chapter_section.hide();
                    })
                    $('#show_quiz_section').click(function () {
                        lms_option_list.hide();
                        lesson_section.hide();
                        quiz_section.show();
                        chapter_section.hide();
                    })
                    // INSIDE
                    $('.show_assignment_section_inside').click(function () {
                        // console.log('clicked');
                        var chapter_id = $(this).data('chapter');
                        $('#assignment_section' + chapter_id).show();
                        $('#lesson_section' + chapter_id).hide();
                        $('#quiz_section' + chapter_id).hide();
                        $('#lms_option_list' + chapter_id).hide();
                        $('#add_option_box' + chapter_id).hide();
                        $('#minus_option_box' + chapter_id).show();
                    })
                    $('.show_lesson_section_inside').click(function () {
                        var chapter_id = $(this).data('chapter');
                        $('#assignment_section' + chapter_id).hide();
                        $('#lesson_section' + chapter_id).show();
                        $('#quiz_section' + chapter_id).hide();
                        $('#lms_option_list' + chapter_id).hide();
                        $('#add_option_box' + chapter_id).hide();
                        $('#minus_option_box' + chapter_id).show();
                    })
                    $('.show_quiz_section_inside').click(function () {
                        var chapter_id = $(this).data('chapter');
                        $('#assignment_section' + chapter_id).hide();
                        $('#lesson_section' + chapter_id).hide();
                        $('#quiz_section' + chapter_id).show();
                        $('#lms_option_list' + chapter_id).hide();
                        $('#add_option_box' + chapter_id).hide();
                        $('#minus_option_box' + chapter_id).show();
                    })
                </script>
            @endpush
            @push('js')
                <script type="text/javascript">
                    // Fees Assign
                    $('.permission-checkAll').on('click', function () {
                        //$('.module_id_'+$(this).val()).prop('checked', this.checked);
                        if ($(this).is(":checked")) {
                            $('.module_id_' + $(this).val()).each(function () {
                                $(this).prop('checked', true);
                            });
                        } else {
                            $('.module_id_' + $(this).val()).each(function () {
                                $(this).prop('checked', false);
                            });
                        }
                    });

                    $('.module_link').on('click', function () {
                        var module_id = $(this).parents('.single_permission').attr("id");
                        var module_link_id = $(this).val();
                        if ($(this).is(":checked")) {
                            $(".module_option_" + module_id + '_' + module_link_id).prop('checked', true);
                        } else {
                            $(".module_option_" + module_id + '_' + module_link_id).prop('checked', false);
                        }
                        var checked = 0;
                        $('.module_id_' + module_id).each(function () {
                            if ($(this).is(":checked")) {
                                checked++;
                            }
                        });

                        if (checked > 0) {
                            $(".main_module_id_" + module_id).prop('checked', true);
                        } else {
                            $(".main_module_id_" + module_id).prop('checked', false);
                        }
                    });

                    $('.module_link_option').on('click', function () {
                        var module_id = $(this).parents('.single_permission').attr("id");
                        var module_link = $(this).parents('.module_link_option_div').attr("id");
                        // module link check
                        var link_checked = 0;
                        $('.module_option_' + module_id + '_' + module_link).each(function () {
                            if ($(this).is(":checked")) {
                                link_checked++;
                            }
                        });

                        if (link_checked > 0) {
                            $("#Sub_Module_" + module_link).prop('checked', true);
                        } else {
                            $("#Sub_Module_" + module_link).prop('checked', false);
                        }
                        // module check
                        var checked = 0;
                        $('.module_id_' + module_id).each(function () {
                            if ($(this).is(":checked")) {
                                checked++;
                            }
                        });

                        if (checked > 0) {
                            $(".main_module_id_" + module_id).prop('checked', true);
                        } else {
                            $(".main_module_id_" + module_id).prop('checked', false);
                        }
                    });
                </script>
            @endpush


            @push('js')
                <script>
                    $(document).on("click", "#create-option", function (event) {
                        $('#multiple-options' + chapter_id).html('');
                        var chapter_id = $(this).data('chapter_id');
                        var number_of_option = $('#number_of_option' + chapter_id).val();
                        console.log('inpur : ' + chapter_id);
                        for (var i = 1; i <= number_of_option; i++) {
                            var appendRow = '';
                            appendRow += "<div class='row  mt-25'>";
                            appendRow += "<div class='col-lg-10'>";
                            appendRow += "<div class='input-effect'>"
                            appendRow += "<input class='primary_input_field name' placeholder='option " + i + "' type='text' name='option[]' autocomplete='off' required>";
                            appendRow += "</div>";
                            appendRow += "</div>";
                            appendRow += "<div class='col-lg-2 mt-15'>";

                            // appendRow += "<input type='checkbox' id='option_check_" + i + "' class='common-checkbox' name='option_check_" + i + "' value='1'>";
                            appendRow += "<label class='primary_checkbox d-flex mr-12 ' for='option_check_" + i + "'>";

                            appendRow += "<input type='checkbox'  id='option_check_" + i + "' name='option_check_" + i + "' value='1'> <span class='checkmark'></span>";
                            appendRow += "</label>";
                            appendRow += "</div>";
                            appendRow += "</div>";

                            $("#multiple-options" + chapter_id).append(appendRow);
                        }
                    });


                    $(".quiz_div input[name='type']").click(function () {
                        var element_key = $(this).data('key');
                        // console.log($(this).val());
                        // console.log($(".quiz_div input[name='type']"));
                        // console.log(element_key);
                        let new_quiz = $('#new_quiz' + element_key);
                        let existing_quiz = $('#existing_quiz' + element_key);
                        if ($(this).val() == 1) {
                            existing_quiz.show();
                            new_quiz.hide();
                            // alert($('input:radio[name=type]:checked').val());
                            //$('#select-table > .roomNumber').attr('enabled',false);
                        } else {
                            existing_quiz.hide();
                            new_quiz.show();
                        }
                    });
                </script>
@endpush
