@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('frontendmanage.Bookmarks')}} @endsection
@section('css') @endsection
@section('js') @endsection

@section('mainContent')

    <x-wish-list-page-section/>

@endsection
