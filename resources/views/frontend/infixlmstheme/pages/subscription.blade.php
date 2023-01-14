@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('frontend.Subscription')}} @endsection
@section('css')
    <link href="{{asset('public/frontend/infixlmstheme/css/subscription.css')}}" rel="stylesheet"/>
@endsection


@section('mainContent')


    <x-breadcrumb :banner="$frontendContent->subscription_page_banner"
                  :title="$frontendContent->subscription_page_title"
                  :subTitle="$frontendContent->subscription_page_sub_title"/>


    <x-subscription-page-section/>


@endsection
@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/subscription.js')}}"></script>
@endsection
