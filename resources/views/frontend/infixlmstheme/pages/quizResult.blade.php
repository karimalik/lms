@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} |    {{$course->title}} @endsection
@section('css')

@endsection
@section('js')

@endsection

@section('mainContent')

    <x-breadcrumb :banner="$frontendContent->quiz_page_banner" :title="trans('frontend.Quiz Result')"
                  :subTitle="$course->title"/>


    <x-quiz-result-page-section :quiz="$quiz" :user="$user" :course="$course"/>




@endsection


