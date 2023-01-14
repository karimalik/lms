<!DOCTYPE html>
<html dir="{{isRtl()?'rtl':''}}" class="{{isRtl()?'rtl':''}}">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{getCourseImage(Settings('favicon'))}}" type="image/png"/>
    <title>
        {{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}}
    </title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    @include('backend.partials.style')
    <script src="{{asset('public/js/common.js')}}"></script>


    <script>
        window.Laravel = {
            "baseUrl": '{{ url("/") }}' + '/',
            "current_path_without_domain": '{{request()->path()}}',
            "csrfToken": '{{csrf_token()}}',
        }
    </script>

    <script>
        window._locale = '{{ app()->getLocale() }}';
        window._translations = {!! json_encode(cache('translations'), JSON_INVALID_UTF8_IGNORE) !!}
    </script>


    <script>
        window.jsLang = function(key, replace) {
            let translation = true

            let json_file = $.parseJSON(window._translations[window._locale]['json'])
            translation = json_file[key]
                ? json_file[key]
                : key
            $.each(replace, (value, key) => {
                translation = translation.replace(':' + key, value)
            })
            return translation
        }

    </script>


    <x-frontend-dynamic-style-color/>

    <script>
        const RTL = "{{isRtl()}}";
        const LANG = "{{ app()->getLocale() }}";
    </script>

    @livewireStyles


</head>

<body class="admin">
@include('preloader')
<input type="hidden" name="demoMode" id="demoMode" value="{{appMode()}}">
<input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
<input type="hidden" name="table_name" id="table_name" value="@yield('table')">
<input type="hidden" name="csrf_token" class="csrf_token" value="{{csrf_token()}}">
<input type="hidden" name="currency_symbol" class="currency_symbol" value="{{Settings('currency_symbol')}}">
<input type="hidden" name="currency_show" class="currency_show" value="{{Settings('currency_show')}}">
<input type="hidden" name="chat_settings" id="chat_settings" value="{{ env('BROADCAST_DRIVER') }}">
<div class="main-wrapper" style="min-height: 600px">
    <!-- Sidebar  -->
@if (isModuleActive('LmsSaas') && Auth::user()->is_saas_admin==1 && Auth::user()->active_panel=='saas')
    @include('lmssaas::partials.sidebar')
@else
    @include('backend.partials.sidebar')
@endif


<!-- Page Content  -->
    <div id="main-content">
@include('backend.partials.menu')
