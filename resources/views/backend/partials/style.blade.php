<link rel="stylesheet" href="{{asset('public/backend/css/jquery-ui.css')}}"/>

<link rel="stylesheet" href="{{asset('public/backend/vendors/font_awesome/css/all.min.css')}}"/>
<link rel="stylesheet" href="{{asset('public/backend/css/themify-icons.css')}}"/>




<link rel="stylesheet" href="{{asset('public/chat/css/style.css')}}">
<link rel="stylesheet" href="{{asset('public/css/preloader.css')}}"/>
@if(isModuleActive("WhatsappSupport"))
    <link rel="stylesheet" href="{{asset('public/whatsapp-support/style.css')}}"/>
@endif


<link rel="stylesheet" href="{{asset('public/backend/css/app.css')}}">


@if(isRtl())
    <link rel="stylesheet" href="{{asset('public/backend/css/backend_style_rtl.css')}}"/>
@else
    <link rel="stylesheet" href="{{asset('public/backend/css/backend_style.css')}}"/>
@endif

@stack('styles')




