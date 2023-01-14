<div class="single_reviews" id="{{$review->id}}_single_reviews">
    <div class="thumb link">
        {{substr($review->userName, 0, 1)}}

        @if(reviewCanDelete($review->userId,$review->instructor_id))
            <a class="position_right deleteBtn" href="#"
               data-toggle="modal"
               onclick="deleteCommnet('{{route('deleteReview',$review->id)}}','{{$review->id}}_single_reviews')"
               data-target="#deleteComment">
                <i class="fas fa-trash  fa-xs"></i>
            </a>
        @endif
    </div>
    <div class="review_content">
        <h4 class="f_w_700">{{$review->userName}}</h4>
        <div class="rated_customer d-flex align-items-center">
            <div class="feedmak_stars">
                @php
                    $main_stars=$review->star;
                    $stars=intval($review->star);
                @endphp
                @for ($i = 0; $i <  $stars; $i++)
                    <i class="fas fa-star"></i>
                @endfor
                @if ($main_stars>$stars)
                    <i class="fas fa-star-half"></i>
                @endif

            </div>
            <span>{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</span>
        </div>
        <p>
            {!! $review->comment !!}
        </p>
    </div>
</div>
