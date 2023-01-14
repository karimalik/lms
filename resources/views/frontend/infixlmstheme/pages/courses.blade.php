@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('courses.Courses')}} @endsection
@section('css') @endsection

@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/classes.js')}}"></script>
@endsection
@section('mainContent')

    <x-breadcrumb :banner="$frontendContent->course_page_banner" :title="$frontendContent->course_page_title"
                  :subTitle="$frontendContent->course_page_sub_title"/>

    <x-course-page-section :request="$request" :categories="$categories" :languages="$languages"/>
@endsection

