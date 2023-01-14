@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} |    {{$course->title}} @endsection
@section('css')
    <link href="{{asset('public/backend/css/summernote-bs4.min.css/')}}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('public/frontend/infixlmstheme/js/quiz_start.js') }}"></script>
    <script src="{{asset('public/backend/js/summernote-bs4.min.js')}}"></script>
    <script>
        if ($('.lms_summernote').length) {
            $('.lms_summernote').summernote({
                placeholder: '',
                tabsize: 2,
                height: 188,
                tooltip: true
            });
        }
    </script>
@endsection

@section('mainContent')


    <x-breadcrumb :banner="$frontendContent->quiz_page_banner" :title="trans('frontend.Start Quiz')"
                  :subTitle="$course->title"/>

    <x-quiz-start-page-section :course="$course" :quizId="$quiz_id"/>


    @include(theme('partials._quiz_submit_confirm_modal'))
    @include(theme('partials._quiz_start_confirm_modal'))


@endsection

