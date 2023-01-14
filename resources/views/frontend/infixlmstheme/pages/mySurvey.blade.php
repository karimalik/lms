@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('survey.Survey')}} @endsection
@section('css') @endsection
@section('js') @endsection

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
                                    <h3 class="mb-0">{{__('survey.Survey')}}</h3>
                                    <h4></h4>
                                </div>
                            </div>
                        </div>
                        @if(count($surveys)==0)
                            <div class="col-12">
                                <div class="section__title3 margin_50">
                                    <p class="text-center">Survey Not Assigned</p>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="table-responsive">
                                        <table class="table custom_table3 mb-0">
                                            <thead>
                                            <tr>
                                                <th scope="col">{{__('common.SL')}}</th>
                                                <th scope="col">{{__('common.Name')}}</th>
{{--                                                <th scope="col">{{ __('student.Group') }}</th>--}}
                                                <th scope="col">{{ __('courses.Course') }}</th>
                                                <th scope="col">Survey For</th>
                                                <th scope="col">{{__('common.Action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($surveys))
                                                @foreach($surveys as $index => $survey)
                                                    @php
                                                        if($survey->survey->title==""){
                                                            continue;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td scope="col">{{ $index+1 }}</td>
                                                        <td scope="col">{{ $survey->survey->title }}</td>
{{--                                                        <td scope="col">{{ $survey->survey->group->name }}</td>--}}
                                                        <td scope="col">{{ $survey->survey->course->title }}</td>
                                                        <td scope="col">{{ $survey->survey->available_for }}</td>

                                                        <td scope="col">
                                                            <a href="{{ route('survey.student_survey_participate', $survey->id) }}" class=" link_value theme_btn small_btn4" type="button">Participatie</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        {{ $surveys->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
