@include(theme('partials._header'))

<div class="dashboard_main_wrapper">
    @include(theme('partials._sidebar'))

    <section class="main_content dashboard_part">
        @include(theme('partials._dashboard_menu'))
        @yield('mainContent')
    </section>
</div>
@include('preloader')
<input type="hidden" name="app_debug" class="app_debug" value="{{env('APP_DEBUG') }}">
@include(theme('partials._footer'))
