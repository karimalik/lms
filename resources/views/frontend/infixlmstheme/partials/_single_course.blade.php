<div class="col-xl-4 col-md-6">
    @if($course->type==1)
        <div class="couse_wizged">
            <div class="thumb">

                <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                    <img class="w-100" src="{{getCourseImage($course->thumbnail)}}" alt="">
                    <x-price-tag :price="$course->price"
                                 :discount="$course->discount_price"/>
                </a>

            </div>
            <div class="course_content">
                <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                    <h4 class="noBrake" title="{{$course->title}}">
                        {{$course->title}}
                    </h4>
                </a>
                <div class="rating_cart">
                    <div class="rateing">
                        <span>{{$course->totalReview}}/5</span>

                        <i class="fas fa-star"></i>
                    </div>
                    @auth()
                        @if(!$course->isLoginUserEnrolled && !$course->isLoginUserCart)
                            <a href="#" class="cart_store"
                               data-id="{{$course->id}}">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    @endauth
                    @guest()
                        @if(!$course->isGuestUserCart)
                            <a href="#" class="cart_store"
                               data-id="{{$course->id}}">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    @endguest

                </div>
                <div class="course_less_students">
                    <a> <i class="ti-agenda"></i> {{count($course->lessons)}}
                        {{__('frontend.Lessons')}}</a>
                    <a>
                        <i class="ti-user"></i> {{$course->total_enrolled}} {{__('frontend.Students')}}
                    </a>
                </div>
            </div>
        </div>
    @elseif($course->type==2)
        <div class="quiz_wizged">
            <div class="thumb">
                <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                    <img class="w-100" src="{{getCourseImage($course->thumbnail)}}" alt="">
                    <x-price-tag :price="$course->price"
                                 :discount="$course->discount_price"/>
                    <span class="quiz_tag">{{__('frontend.Quiz')}}</span>
                </a>

            </div>
            <div class="course_content">
                <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                    <h4 class="noBrake" title="{{$course->title}}"> {{$course->title}}</h4>
                </a>
                <div class="rating_cart">
                    <div class="rateing">
                        <span>{{$course->totalReview}}/5</span>
                        <i class="fas fa-star"></i>
                    </div>
                    @auth()
                        @if(!$course->isLoginUserEnrolled && !$course->isLoginUserCart)
                            <a href="#" class="cart_store"
                               data-id="{{$course->id}}">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    @endauth
                    @guest()
                        @if(!$course->isGuestUserCart)
                            <a href="#" class="cart_store"
                               data-id="{{$course->id}}">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    @endguest
                </div>
                <div class="course_less_students">
                    <a> <i class="ti-agenda"></i>
                        {{count($course->quiz->assign)}}
                        {{__('frontend.Question')}}</a>
                    <a>
                        <i class="ti-user"></i> {{$course->total_enrolled}} {{__('frontend.Students')}}
                    </a>
                </div>
            </div>
        </div>

    @elseif($course->type==3)
        <div class="quiz_wizged">
            <div class="thumb">
                <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                    <img class="w-100" src="{{getCourseImage($course->thumbnail)}}" alt="">

                    <x-price-tag :price="$course->price"
                                 :discount="$course->discount_price"/>

                    <span class="live_tag">{{__('common.Live')}}</span>
                </a>

            </div>
            <div class="course_content">
                <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                    <h4 class="noBrake" title="{{$course->title}}"> {{$course->title}}</h4>
                </a>
                <div class="rating_cart">
                    <div class="rateing">
                        <span>{{$course->totalReview}}/5</span>
                        <i class="fas fa-star"></i>
                    </div>
                    @auth()
                        @if(!$course->isLoginUserEnrolled && !$course->isLoginUserCart)
                            <a href="#" class="cart_store"
                               data-id="{{$course->id}}">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    @endauth
                    @guest()
                        @if(!$course->isGuestUserCart)
                            <a href="#" class="cart_store"
                               data-id="{{$course->id}}">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    @endguest
                </div>
                <div class="course_less_students">

                    <a> <i
                            class="ti-agenda"></i> {{$course->class->total_class}}
                        {{__('frontend.Classes')}}</a>
                    <a>
                        <i class="ti-user"></i> {{$course->total_enrolled}} {{__('frontend.Students')}}
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
