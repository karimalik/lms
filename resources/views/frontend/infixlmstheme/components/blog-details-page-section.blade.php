<div class="lms_blog_details_area">
    <div class="container">
        <div class="row justify-content-center ">
            <div class="col-xl-7 col-lg-7 ">
                <div class="blog_details_inner">
                    <div class="blog_details_banner">
                        <img class="w-100" src="{{getBlogImage($blog->image)}}" alt="">
                    </div>

                    <div class="blog_post_date d-flex align-items-center"><p>{{$blog->user->name}}
                            . {{ showDate(@$blog->authored_date ) }}</p></div>
                    <h3>{{$blog->title}}</h3>
                    <p class="mb_25">

                        {!! $blog->description !!}
                    </p>
                    <br>

                    <x-blog-details-share-section :blog="$blog"/>
                </div>
                <div class="blog_reviews">
                    <h3 class="font_30 f_w_800 mb_35 lh-1">{{__('frontend.Comments')}}</h3>
                    <div class="blog_reviews_inner">
                        @foreach($blog->comments as $comment)
                            @php
                                if (empty($comment->user_id)){
                                    $name =$comment->name;
                                }else{
                                    $name =$comment->user->name;
                                }

                            @endphp
                            <div class="lms_single_reviews">
                                <div class="thumb">
                                    {{substr($name,0,2)}}
                                </div>
                                <div class="review_content">
                                    <div
                                        class="review_content_head d-flex justify-content-between align-items-start flex-wrap">
                                        <div class="review_content_head_left">
                                            <h4 class="f_w_700 font_20">{{$name}}</h4>
                                            <div class="rated_customer d-flex align-items-center">
                                                <span>{{$comment->created_at->diffforhumans()}}</span>
                                            </div>
                                        </div>
                                        <div class="comment_box_text link">


                                            <a class="position_right reply_btn     mr_20"
                                               data-comment="{{@$comment->id}}" href="#">
                                                {{__('frontend.Reply') }}
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                            @if(blogCommentCanDelete())
                                                <a class="position_right deleteBtn" href="#"
                                                   data-toggle="modal"
                                                   onclick="deleteCommnet('{{route('deleteBlogComment',$comment->id)}}','{{$comment->id}}_single_comment')"
                                                   data-target="#deleteComment">
                                                    <i class="fas fa-trash  fa-xs"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <p>{{$comment->comment}}</p>
                                </div>
                            </div>

                            <div class=" replyBox d-none inputForm reply_form_{{@$comment->id}}  pr-0  w-100">
                                <form action="{{route('blogCommentSubmit')}}" method="post">
                                    <input type="hidden" name="blog_id" value="{{$blog->id}}">
                                    <input type="hidden" name="type" value="2">
                                    @csrf
                                    <input type="hidden" name="comment_id"
                                           value="{{@$comment->id}}">
                                    <div class="row">
                                        @guest()
                                            <div class="col-lg-6">
                                                <input name="name" placeholder="{{__('common.Enter Full Name')}}"
                                                       onfocus="this.placeholder = ''"
                                                       onblur="this.placeholder = '{{__('common.Enter Full Name')}}'"
                                                       class="primary_input5 bg_style1   "
                                                       required="" type="text">
                                                <div class="rounded_border_bottom mb_20"></div>
                                            </div>
                                            <div class="col-6">
                                                <input name="email" placeholder="{{__('common.Enter Email Address')}}"
                                                       onfocus="this.placeholder = ''"
                                                       onblur="this.placeholder = '{{__('common.Enter Email Address')}}'"
                                                       class="primary_input5  "
                                                       required="" type="email">
                                                <div class="rounded_border_bottom mb_20"></div>
                                            </div>
                                        @endguest
                                        <div class="col-12">
                                <textarea name="comment" placeholder="{{__('common.Write your comments here')}}…"
                                          onfocus="this.placeholder = ''"
                                          onblur="this.placeholder = '{{__('common.Write your comments here')}}…'"
                                          class="primary_textarea5   " required=""></textarea>
                                            <div class="rounded_border_bottom mb_20"></div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit"
                                                    class="theme_btn small_btn2 w-100  text-center   text-uppercase  text-center mb_25">
                                                {{__('frontend.Reply')}}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            @foreach ($comment->replies as $replay)
                                @php
                                    if (empty($replay->user_id)){
                                        $name =$replay->name;
                                    }else{
                                        $name =$replay->user->name;
                                    }

                                @endphp
                                <div class="lms_single_reviews replyBox">
                                    <div class="thumb">
                                        {{substr($name,0,2)}}
                                    </div>
                                    <div class="review_content">
                                        <div
                                            class="review_content_head d-flex justify-content-between align-items-start flex-wrap">
                                            <div class="review_content_head_left">
                                                <h4 class="f_w_700 font_20">{{$name}}</h4>
                                                <div class="rated_customer d-flex align-items-center">

                                                    <span>{{$comment->created_at->diffforhumans()}}</span>
                                                </div>
                                            </div>
                                            <div class="comment_box_text link">
                                                @if(blogCommentCanDelete())
                                                    <a class="position_right deleteBtn" href="#"
                                                       data-toggle="modal"
                                                       onclick="deleteCommnet('{{route('deleteBlogComment',$replay->id)}}','{{$replay->id}}_single_reply_comment')"
                                                       data-target="#deleteComment">
                                                        <i class="fas fa-trash  fa-xs"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <p> {{$replay->comment}}</p>
                                    </div>
                                </div>


                            @endforeach
                        @endforeach

                    </div>
                </div>
                <div class="blog_reply_box mb_30 blogComment">
                    <h3 class="font_30 f_w_800  lh-1 mb_5 ">{{__('frontend.Leave a comment')}}</h3>
                    <form action="{{route('blogCommentSubmit')}}" method="post">
                        <input type="hidden" name="blog_id" value="{{$blog->id}}">
                        <input type="hidden" name="type" value="1">
                        @csrf
                        <div class="row">
                            @guest()
                                <div class="col-lg-6">
                                    <input name="name" placeholder="{{__('common.Enter Full Name')}}"
                                           onfocus="this.placeholder = ''"
                                           onblur="this.placeholder = '{{__('common.Enter Full Name')}}'"
                                           class="primary_input5 bg_style1   "
                                           required="" type="text">
                                    <div class="rounded_border_bottom mb_20"></div>
                                </div>
                                <div class="col-6">
                                    <input name="email" placeholder="{{__('common.Enter Email Address')}}"
                                           onfocus="this.placeholder = ''"
                                           onblur="this.placeholder = '{{__('common.Enter Email Address')}}'"
                                           class="primary_input5  "
                                           required="" type="email">
                                    <div class="rounded_border_bottom mb_20"></div>
                                </div>
                            @endguest
                            <div class="col-12">
                                <textarea name="comment" placeholder="{{__('common.Write your comments here')}}…"
                                          onfocus="this.placeholder = ''"
                                          onblur="this.placeholder = '{{__('common.Write your comments here')}}…'"
                                          class="primary_textarea5   " required=""></textarea>
                                <div class="rounded_border_bottom mb_20"></div>
                            </div>
                            <div class="col-12">
                                <button type="submit"
                                        class="theme_btn small_btn2 w-100  text-center   text-uppercase  text-center">
                                    {{__('frontend.Post comment')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3">
                <x-blog-sidebar-section :tag="$blog->tags"/>
            </div>
        </div>
    </div>
    @include(theme('partials._delete_model'))
</div>
