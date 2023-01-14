@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('quiz.Quiz')}}@endsection
@section('css') @endsection

@section('js')
    <script src="{{ asset('public/frontend/infixlmstheme/js/classes.js') }}"></script>
@endsection
@section('mainContent')

    <x-breadcrumb :banner="$frontendContent->quiz_page_banner" :title="$frontendContent->quiz_page_title"
                  :subTitle="$frontendContent->quiz_page_sub_title"/>

    <x-quiz-page-section :request="$request" :categories="$categories" :languages="$languages"/>



@endsection






