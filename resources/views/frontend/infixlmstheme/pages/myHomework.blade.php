@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('homework.Study Material List')}} @endsection
@section('css')

 @endsection
@section('js')

@endsection

@section('mainContent')
    <x-my-homework />
@endsection
