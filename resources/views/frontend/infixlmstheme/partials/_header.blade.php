<!doctype html>
<html dir="{{isRtl()?'rtl':''}}" class="{{isRtl()?'rtl':''}}" lang="en" itemscope
      itemtype="{{url('/')}}">

<head>
    @laravelPWA
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <meta property="og:url" content="{{url()->current()}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{Settings('site_title')}}"/>
    <meta property="og:description" content="{{Settings('footer_about_description')}}"/>
    <meta property="og:image" content=" @yield('og_image')"/>

    <title>
        @yield('title')
    </title>
@if(!empty(Settings('google_analytics') ))
    <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{Settings('google_analytics') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', '{{Settings('google_analytics') }}');
        </script>
@endif
<!-- Google / Search Engine Tags -->
    <meta itemprop="name" content="{{ Settings('site_name')  }}">

    <meta itemprop="image" content="{{asset(Settings('logo') )}}">
    @if(routeIs('frontendHomePage'))
        <meta itemprop="description" content="{{ Settings('meta_description')  }}">
        <meta property="og:description" content="{{ Settings('meta_description')  }}">
        <meta itemprop="keywords" content="{{ Settings('meta_keywords') }}">

    @elseif(routeIs('courseDetailsView'))
        <meta itemprop="description" content="{{ $course->meta_description  }}">
        <meta property="og:description" content="{{ $course->meta_description  }}">
        <meta itemprop="keywords" content="{{ $course->meta_keywords }}">
    @elseif(routeIs('quizDetailsView'))
        <meta itemprop="description" content="{{ $course->meta_description  }}">
        <meta property="og:description" content="{{ $course->meta_description  }}">
        <meta itemprop="keywords" content="{{ $course->meta_keywords }}">
    @endif
    <meta itemprop="author" content="InfixLMS">

    <!-- Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ Settings('site_title')  }}">

    <meta property="og:image" content="{{asset(Settings('logo') )}}"/>
    <meta property="og:image:type" content="image/png"/>
    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset(Settings('favicon') )}}">
    <!-- Place favicon.ico in the root directory -->


    <x-frontend-dynamic-style-color/>


    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/fontawesome.css ">
    <link rel="stylesheet" href="{{asset('public/backend/css/themify-icons.css')}}"/>
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/flaticon.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/nice-select.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/notification.css">

    <link href="{{asset('public/backend/css/summernote-bs4.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/css/preloader.css')}}"/>

    @if(str_contains(request()->url(), 'chat'))
        <link rel="stylesheet" href="{{asset('public/backend/css/jquery-ui.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/backend/vendors/select2/select2.css')}}"/>
        <link rel="stylesheet" href="{{asset('public/chat/css/style-student.css')}}">
    @endif

    @if(auth()->check() && auth()->user()->role_id == 3 && !str_contains(request()->url(), 'chat'))
        <link rel="stylesheet" href="{{asset('public/chat/css/notification.css')}}">
    @endif

    @if(isModuleActive("WhatsappSupport"))
        <link rel="stylesheet" href="{{ asset('public/whatsapp-support/style.css') }}">
    @endif
    <script>
        window.Laravel = {
            "baseUrl": '{{ url('/') }}' + '/',
            "current_path_without_domain": '{{request()->path()}}',
            "csrfToken": '{{csrf_token()}}',
        }
    </script>

    <script>
        window._locale = '{{ app()->getLocale() }}';
        window._translations = {!! json_encode(cache('translations'), JSON_INVALID_UTF8_IGNORE) !!}
    </script>

    @if(auth()->check() && auth()->user()->role_id == 3)
        <style>
            .admin-visitor-area {
                margin: 0 30px 30px 30px !important;
            }

            .dashboard_main_wrapper .main_content_iner.main_content_padding {
                padding-top: 50px !important;
            }

            .primary_input {
                height: 50px;
                border-radius: 0px;
                border: unset;
                font-family: "Jost", sans-serif;
                font-size: 14px;
                font-weight: 400;
                color: unset;
                padding: unset;
                width: 100%;
                @if($errors->any())
                      margin-bottom: 5px;
                @else
     margin-bottom: 30px;
            @endif






            }

            .primary_input_field {
                border: 1px solid #ECEEF4;
                font-size: 14px;
                color: #415094;
                padding-left: 20px;
                height: 46px;
                border-radius: 30px;
                width: 100%;
                padding-right: 15px;
            }

            .primary_input_label {
                font-size: 12px;
                text-transform: uppercase;
                color: #828BB2;
                display: block;
                margin-bottom: 6px;
                font-weight: 400;
            }

            .chat_badge {
                color: #ffffff;
                border-radius: 20px;
                font-size: 10px;
                position: relative;
                left: -20px;
                top: -12px;
                padding: 0px 4px !important;
                max-width: 18px;
                max-height: 18px;
                box-shadow: none;
                background: #ed353b;
            }

            .chat-icon-size {
                font-size: 1.35em;
                color: #687083;
            }
        </style>
    @endif


    @if(!empty(Settings('facebook_pixel')))
    <!-- Facebook Pixel Code -->
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', {{Settings('facebook_pixel')}});
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                 src="https://www.facebook.com/tr?id={{Settings('facebook_pixel')}}/&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
    @endif


    <input type="hidden" id="baseUrl" value="{{url('/')}}">
    <input type="hidden" name="chat_settings" id="chat_settings" value="{{ env('BROADCAST_DRIVER') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/app.css">
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme') }}/css/frontend_style.css">
    <script src="{{asset('public/js/common.js')}}"></script>
    @yield('css')

</head>

<body>

