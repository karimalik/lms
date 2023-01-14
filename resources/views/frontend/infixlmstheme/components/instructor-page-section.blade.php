<div class="instractos_details_wrapper">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="instractos_profile mb_30">
                    <div class="thumb">
                        <img src="{{getInstructorImage($instructor->image)}}" alt="#">
                    </div>
                    <div class="instractor_meta">
                        <h4>{{$instructor->name}}</h4>
                        <span>{{$instructor->headline}}</span>
                        <div class="social_media">
                            @if(!empty($instructor->facebook))
                                <a href="https://facebook.com/{{$instructor->facebook}}" class="facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if(!empty($instructor->twitter))
                                <a href="https://twitter.com/{{$instructor->twitter}}" class="twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if(!empty($instructor->instagram))
                                <a href="https://instagram.com/{{$instructor->instagram}}" class="pinterest">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if(!empty($instructor->linkedin))
                                <a href="https://linkedin.com/{{$instructor->linkedin}}" class="linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 offset-xl-1">
                <div class="instractos_main_details mb_30">
                    <div class="course__details_title">
                        <div class="single__details">
                            <div class="details_content">
                                <span>{{__('frontend.Student')}}</span>
                                <h4 class="f_w_700">{{$students}} {{__('frontend.Students')}}</h4>
                            </div>
                        </div>
                        <div class="single__details">
                            <div class="details_content">
                                <span>{{__('frontend.Category')}}</span>
                                <h4 class="f_w_700">{{$instructor->category()}}</h4>
                            </div>
                        </div>
                        <div class="single__details">
                            <div class="details_content">
                                <span>{{__('frontend.Reviews')}}</span>
                                <div class="rating_star">
                                    <div class="stars">
                                        @php
                                            $total =$instructor->totalRating();
                                            $totalReviews =$total['total'];
                                            $rating =$total['rating'];
                                                    $main_stars=$rating;
                                                    $stars=intval($main_stars);

                                        @endphp
                                        @for ($i = 0; $i <  $stars; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                        @if ($main_stars>$stars)
                                            <i class="fas fa-star-half"></i>
                                        @endif
                                        @if($main_stars==0)
                                            @for ($i = 0; $i <  5; $i++)
                                                <i class="far fa-star"></i>
                                            @endfor
                                        @endif

                                    </div>


                                    <p>{{($rating)}}/5 ({{$totalReviews}} rating)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="instractos_info_Details">
                        <p>
                            {!! $instructor->about !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
