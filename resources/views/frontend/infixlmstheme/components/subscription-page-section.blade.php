<div>
    <div class="contact_section ">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="contact_address">
                        <div class="row justify-content-center">
                            <div class="col-xl-12">
                                <div class="row justify-content-between">
                                    <div class="col-lg-12">


                                        <section class="pricing_plan pt_100   bg-white">
                                            <div class="container">
                                                <div class="row justify-content-center">
                                                    <div class="col-lg-6">
                                                        <div class="section_tittle"><h2
                                                            >{{__('subscription.Pricing Plan & Package')}}</h2>
                                                            <p></p></div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    @foreach($plans as $key2=>$plan)
                                                        <div class="col-lg-4 col-sm-6 ">
                                                            <div
                                                                class="single_pricing_plan row_padding">
                                                                <h5>{{$plan->title}}</h5>
                                                                @if($setting->type==1)
                                                                    <h4>{{__('subscription.All Courses')}}</h4>
                                                                @elseif($setting->type==2)
                                                                    <h4>
                                                                        <a href="{{route('subscriptionCourseList',$plan->id)}}">{{__('subscription.Selected Courses')}}</a>
                                                                    </h4>
                                                                @endif
                                                                <h2>
                                                                    @php
                                                                        $type =Settings('currency_show');
                                                                    @endphp
                                                                    <span
                                                                        class="@if($type==3|| $type==4) right @endif">{{Settings('currency_symbol') }}</span>

                                                                    {{$plan->price}}.00</h2>
                                                                <p class="pb-2">{{$plan->about}}</p>
                                                                <form action="{{route('courseSubscriptionCheckout')}}">
                                                                    <input type="hidden" name="price"
                                                                           value="{{$plan->price}}">
                                                                    <input type="hidden" name="plan"
                                                                           value="{{$plan->id}}">
                                                                    <button type="submit"
                                                                            class="theme_btn small_btn2 payment-link">
                                                                        {{$plan->btn_txt}}
                                                                    </button>
                                                                </form>


                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>


                                                <div class="row justify-content-center">
                                                    <div class="col-lg-8">
                                                        <div class="features_list  pt_100 pb_100 list_style  "><h5
                                                            >{{__('subscription.Included features')}} <span
                                                                >({{__('subscription.These features for both of the plan')}})</span>
                                                            </h5>
                                                            <ul>
                                                                @foreach($plan_features as $key=>$feature)
                                                                    <li>
                                                                        <i class="fas fa-check-circle"></i>
                                                                        {{$feature->title}}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>


                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <h3 style="    text-align: center;
    margin-bottom: 72px;"> {{__('subscription.Frequently Ask Question')}} </h3>
                                        <div class="theme_according mb_100" id="accordion1">
                                            @foreach($faqs as $key=>$faq)
                                                <div class="card">
                                                    <div class="card-header pink_bg" id="headingFour{{$key}}">
                                                        <h5 class="mb-0">
                                                            <button class="btn btn-link text_white collapsed"
                                                                    data-toggle="collapse"
                                                                    data-target="#collapseFour{{$key}}"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseFour{{$key}}">
                                                                {{$faq->question}}
                                                            </button>
                                                        </h5>
                                                    </div>
                                                    <div class="collapse" id="collapseFour{{$key}}"
                                                         aria-labelledby="headingFour{{$key}}"
                                                         data-parent="#accordion1">
                                                        <div class="card-body">
                                                            <div class="curriculam_list">

                                                                <div class="curriculam_single">
                                                                    <div class="curriculam_left">

                                                                        <span>{!! $faq->answer !!}</span>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
