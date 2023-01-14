<div>


    @if ($modal)

        <style>
            .newsletter_form_wrapper .newsletter_form_inner .newsletter_form_thumb {
                height: 100%;
                background-image: url({{ asset($popup->image) }});
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
            }
        </style>

        <div class="d-none" id="popupContentDiv">
            <div class="newsletter_form_wrapper newsletter_active" id="popupContentModal">
                <div class="newsletter_form_inner">
                    <div class="close_modal" onclick="closePopUpModel()">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="newsletter_form_thumb">
                    </div>
                    <div class="newsletter_form text-center">
                        <h3>{{$popup->title}}</h3>
                        <p>
                            {!! $popup->message !!}
                        </p>

                        <div class="row">
                            <div class="col-12 mt-10">
                                <a href="{{$popup->link}}"
                                   class="theme_btn w-100 text-center"> {{$popup->btn_txt}}</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <script>
            setTimeout(function () {
                $("#popupContentDiv").removeClass('d-none');
            }, 3000);

            function closePopUpModel() {
                $("#popupContentDiv").addClass('d-none');
            }
        </script>
    @endif


</div>
