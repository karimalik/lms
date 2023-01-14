<div>
    @if($newsletterSetting)
        @if($newsletterSetting->home_status==1)
            <div class="footer_top_area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="footer__cta">
                                <div class="thumb">
                                    <img src="{{asset(@$frontendContent->subscribe_logo)}}" alt="" class="w-100">
                                </div>
                                <div class="cta_content">
                                    <h3>{{@$frontendContent->subscribe_title}}</h3>
                                    <p>{{@$frontendContent->subscribe_sub_title}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="subcribe-form theme_mailChimp">
                                <form action="{{route('subscribe')}}"
                                      method="POST" class="subscription relative">@csrf
                                    <input name="email" class="form-control"
                                           placeholder="{{__('frontend.Enter e-mail Address')}}"
                                           onfocus="this.placeholder = ''"
                                           onblur="this.placeholder = '{{__('frontend.Email')}}'"
                                           required="" type="email" value="{{old('email')}}">

                                    <button type="submit">{{__('frontend.Subscribe Now')}}</button>
                                    <div class="info">
                                        @if(isset($errors) && $errors->any())
                                            <span class="text-danger" role="alert">{{$errors->first('email')}}</span>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    @endif
</div>
