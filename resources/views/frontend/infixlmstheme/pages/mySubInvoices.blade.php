@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | Invoice @endsection
@section('css')
    <link href="{{asset('public/frontend/infixlmstheme/css/my_invoice.css')}}" rel="stylesheet"/>
@endsection
@section('mainContent')

    <x-subscription-invoice-page-section :enroll="$enroll"/>

@endsection
@section('js')
    <script src="{{ asset('public/frontend/infixlmstheme') }}/js/html2pdf.bundle.js"></script>
    <script src="{{ asset('public/frontend/infixlmstheme/js/my_invoice.js') }}"></script>
@endsection
