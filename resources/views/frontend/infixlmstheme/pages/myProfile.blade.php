@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('frontendmanage.My Profile')}} @endsection
@section('css')
    <link href="{{asset('public/frontend/infixlmstheme/css/select2.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/checkout.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/myProfile.css')}}" rel="stylesheet"/>
@endsection
@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/select2.min.js')}}"></script>
    <script src="{{ asset('public/frontend/infixlmstheme/js/my_profile.js') }}"></script>

    <script src="{{asset('public/frontend/infixlmstheme/js/city.js')}}"></script>


    <script src="{{asset('public/backend/js/summernote-bs4.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.primary_textarea4 ').summernote({
                placeholder: 'Write here',
                tabsize: 2,
                height: 188,
                tooltip: true
            });
        });
    </script>
@endsection

@section('mainContent')

    <x-my-profile-page-section/>

@endsection
