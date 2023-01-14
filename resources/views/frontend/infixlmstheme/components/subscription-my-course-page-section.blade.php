<div>
    <div class="main_content_iner main_content_padding">
        <div class="container">
            <div class="my_courses_wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="section__title3 margin-50">
                            <h3>

                                {{__('subscription.Subscription')}} <small>
                                    ( Validity {{\Illuminate\Support\Facades\Auth::user()->subscription_validity_date}})
                                </small>

                            </h3>
                        </div>
                    </div>
                    @if(isset($courses))

                        @foreach ($courses as $SingleCourse)
                            @php
                                $course=$SingleCourse->course;
                            @endphp
                            <div class="col-xl-4 col-md-6">
                                @if($course->type==1)
                                    <div class="couse_wizged">
                                        <div class="thumb">
                                            <div class="thumb_inner lazy" data-src="{{ file_exists($course->thumbnail) ? asset($course->thumbnail) : asset('public/\uploads/course_sample.png') }}">

                                                <x-price-tag :price="$course->price"
                                                             :discount="$course->discount_price"/>

                                            </div>

                                        </div>
                                        <div class="course_content">
                                            <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                                                <h4 class="noBrake" title="{{$course->title}}">
                                                    {{$course->title}}
                                                </h4>
                                            </a>
                                            <div class="d-flex align-items-center gap_15">
                                                <div class="rating_cart">
                                                    <div class="rateing">
                                                        <span>{{$course->totalReview}}/5</span>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                </div>

                                                <div class="progress_percent flex-fill text-right">
                                                    <div class="progress theme_progressBar ">
                                                        <div class="progress-bar" role="progressbar"
                                                             style="width: {{round($course->loginUserTotalPercentage)}}%"
                                                             aria-valuenow="25"
                                                             aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <p class="font_14 f_w_400">{{round($course->loginUserTotalPercentage)}}
                                                        % {{__('student.Complete')}}</p>
                                                </div>
                                            </div>
                                            <div class="course_less_students">
                                                <a>
                                                    <i class="ti-agenda"></i> {{count($course->lessons)}} {{__('student.Lessons')}}
                                                </a>
                                                <a>
                                                    <i class="ti-user"></i> {{$course->total_enrolled}} {{__('student.Students')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($course->type==2)
                                    <div class="quiz_wizged">
                                        <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                                            <div class="thumb">
                                                <div class="thumb_inner lazy" data-src="{{ file_exists($course->thumbnail) ? asset($course->thumbnail) : asset('public/\uploads/course_sample.png') }}">

                                                    <x-price-tag :price="$course->price"
                                                                 :discount="$course->discount_price"/>


                                                </div>
                                                <span class="quiz_tag">{{__('quiz.Quiz')}}</span>
                                            </div>
                                        </a>
                                        <div class="course_content">
                                            <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                                                <h4 class="noBrake" title="{{$course->title}}"> {{$course->title}}</h4>
                                            </a>
                                            <div class="rating_cart">
                                                <div class="rateing">
                                                    <span>{{$course->totalReview}}/5</span>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            </div>
                                            <div class="course_less_students">

                                                <a> <i class="ti-agenda"></i>{{count($course->quiz->assign)}}
                                                    {{__('student.Question')}}</a>
                                                <a>
                                                    <i class="ti-user"></i> {{$course->total_enrolled}} {{__('student.Students')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                @elseif($course->type==3)
                                    <div class="quiz_wizged">
                                        <div class="thumb">
                                            <a href="{{courseDetailsUrl($course->id,$course->type,$course->slug)}}">
                                                <div class="thumb">
                                                    <div class="thumb_inner lazy" data-src="{{ file_exists($course->thumbnail) ? asset($course->thumbnail) : asset('public/\uploads/course_sample.png') }}">

                                                        <x-price-tag :price="$course->price"
                                                                     :discount="$course->discount_price"/>


                                                    </div>
                                                    <span class="live_tag">{{__('student.Live')}}</span>
                                                </div>
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
                                            </div>
                                            <div class="course_less_students">
                                                <a> <i
                                                        class="ti-agenda"></i> {{$course->class->total_class}}
                                                    {{__('student.Classes')}}</a>
                                                <a>
                                                    <i class="ti-user"></i> {{$course->total_enrolled}} {{__('student.Students')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                    @if(count($courses)==0)
                        <div class="col-12">
                            <div class="section__title3 margin_50">
                                @if( routeIs('myClasses'))
                                    <p class="text-center">{{__('student.No Class Purchased Yet')}}!</p>
                                @elseif( routeIs('myQuizzes'))
                                    <p class="text-center">{{__('student.No Quiz Purchased Yet')}}!</p>
                                @else
                                    <p class="text-center">{{__('student.No Course Purchased Yet')}}!</p>
                                @endif

                            </div>
                        </div>
                    @endif

                    {{ $courses->appends(Request::all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
