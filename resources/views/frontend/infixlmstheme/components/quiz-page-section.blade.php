<div>
    <input type="hidden" class="class_route" name="class_route" value="{{route('quizzes')}}">

    <div class="courses_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-xl-3">
                    <x-class-page-section-sidebar :level="$level" :type="$type" :categories="$categories"
                                                  :category="$category" :languages="$languages" :language="$language" :mode="$mode"/>
                </div>
                <div class="col-lg-8 col-xl-9">
                    <div class="row">
                        <div class="col-12">
                            <div class="box_header d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="font_16 f_w_500 mb_30">{{$total}} {{__('frontend.Quiz are found')}}</h5>
                                <div class="box_header_right mb_30">
                                    <div class="short_select d-flex align-items-center">
                                        <h5 class="mr_10 font_16 f_w_500 mb-0">{{__('frontend.Order By')}}:</h5>
                                        <select class="small_select" id="order">
                                            <option data-display="None">{{__('frontend.None')}}</option>
                                            <option
                                                value="price" {{$order=="price"?'selected':''}}>{{__('frontend.Price')}}</option>
                                            <option
                                                value="date" {{$order=="date"?'selected':''}}>{{__('frontend.Date')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(isset($courses))
                            @foreach ($courses as $course)
                                <div class="col-lg-6 col-xl-4">


                                    <div class="quiz_wizged mb_30">
                                        <a href="{{courseDetailsUrl(@$course->id,@$course->type,@$course->slug)}}">
                                            <div class="thumb">
                                                <div class="thumb_inner lazy" data-src="{{ file_exists($course->thumbnail) ? asset($course->thumbnail) : asset('public/\uploads/course_sample.png') }}">
                                                </div>

                                                <x-price-tag :price="$course->price"
                                                             :discount="$course->discount_price"/>

                                                <span class="quiz_tag">{{__('quiz.Quiz')}}</span>
                                            </div>

                                        </a>

                                        <div class="course_content">
                                            <a href="{{courseDetailsUrl(@$course->id,@$course->type,@$course->slug)}}">
                                                <h4 class="noBrake" title=" {{$course->title}}">
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
                                                <a> <i class="ti-agenda"></i>
                                                    {{count($course->quiz->assign)}}
                                                    {{__('frontend.Question')}}</a>
                                                <a>
                                                    <i class="ti-user"></i> {{$course->total_enrolled}} {{__('frontend.Students')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        @endif
                        @if(count($courses)==0)
                            <div class="col-lg-12">

                                <div class="Nocouse_wizged text-center d-flex align-items-center justify-content-center">
                                    <div class="thumb">
                                        <img style="width: 50px"
                                             src="{{ asset('public/frontend/infixlmstheme') }}/img/not-found.png"
                                             alt="">
                                    </div>
                                    <h1>
                                        {{__('frontend.No Quiz Found')}}
                                    </h1>
                                </div>


                            </div>

                        @endif
                    </div>

                    {{ $courses->appends(Request::all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
