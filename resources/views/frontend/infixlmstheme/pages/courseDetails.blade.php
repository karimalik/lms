


@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} |  {{$course->title}} @endsection
@section('og_image'){{asset($course->image)}}@endsection
@section('css')
    <style>
        .course__details .video_screen {
            background-image: url('{{getCourseImage(@$course->image)}}');
        }

        iframe {
            position: relative !important;
        }
    </style>
    <link href="{{asset('public/frontend/infixlmstheme/css/videopopup.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/video.popup.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/frontend/infixlmstheme/css/class_details.css')}}" rel="stylesheet"/>


@endsection


@section('mainContent')

    <x-breadcrumb :banner="$frontendContent->course_page_banner"
                  :title="trans('frontend.Course Details')"
                  :subTitle="$course->title"/>



    <x-course-deatils-page-section :course="$course" :request="$request" :isEnrolled="$isEnrolled"/>


    @if ($course->host=='VdoCipher')

        @include(theme('partials._player_modal'))
    @endif


@endsection

@section('js')

    <script src="{{asset('public/frontend/infixlmstheme/js/class_details.js')}}"></script>
    <script src="{{asset('public/frontend/infixlmstheme/js/videopopup.js')}}"></script>
    <script src="{{asset('public/frontend/infixlmstheme/js/video.popup.js')}}"></script>

    <script>


        $("#formSubmitBtn").on("click", function (e) {
            e.preventDefault();

            var form = $('#deleteCommentForm');
            var url = form.attr('action');
            var element = form.data('element');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function (data) {

                }
            });
            var el = '#' + element;
            $(el).hide('slow');
            $('#deleteComment').modal('hide');

        });
    </script>

    <script>
        function deleteCommnet(item, element) {
            let form = $('#deleteCommentForm')
            form.attr('action', item);
            form.attr('data-element', element);
        }
    </script>


    <script>

        var SITEURL = "{{courseDetailsUrl($course->id,$course->type,$course->slug)}}";
        var page = 1;
        load_more(page);
        $(window).scroll(function () { //detect page scroll
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                page++;
                load_more(page);
            }


        });

        function load_more(page) {
            $.ajax({
                url: SITEURL + "?page=" + page,
                type: "get",
                datatype: "html",
                data: {'type': 'comment'},
                beforeSend: function () {
                    $('.ajax-loading').show();
                }
            })
                .done(function (data) {
                    if (data.length == 0) {

                        //notify user if nothing to load
                        $('.ajax-loading').html("");
                        return;
                    }
                    $('.ajax-loading').hide(); //hide loading animation once data is received
                    $("#conversition_box").append(data); //append data into #results element


                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log('No response from server');
                });

        }


        load_more_review(page);


        $(window).scroll(function () { //detect page scroll
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                page++;
                load_more_review(page);
            }


        });

        function load_more_review(page) {
            $.ajax({
                url: SITEURL + "?page=" + page,
                type: "get",
                datatype: "html",
                data: {'type': 'review'},
                beforeSend: function () {
                    $('.ajax-loading').show();
                }
            })
                .done(function (data) {
                    if (data.length == 0) {

                        //notify user if nothing to load
                        $('.ajax-loading').html("");
                        return;
                    }
                    $('.ajax-loading').hide(); //hide loading animation once data is received
                    $("#customers_reviews").append(data); //append data into #results element


                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log('No response from server');
                });

        }
    </script>


@endsection
