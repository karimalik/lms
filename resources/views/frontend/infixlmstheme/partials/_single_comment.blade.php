<div class="single_comment_box" id="{{$comment->id}}_single_comment">
    <div class="comment_box_inner">
        <div class="comment_box_info">
            <div class="thumb">
                <div
                    class="profile_info profile_img collaps_icon d-flex align-items-center">
                    <div class="studentProfileThumb"
                         style="background-image: url('{{getProfileImage(@$comment->user['image'])}}');margin: 0"></div>

                </div>

            </div>
            <div class="comment_box_text link">
                @if ($isEnrolled)
                    <a class="position_right reply_btn   @if(commentCanDelete($comment->id,$comment->instructor_id)) mr_20 @endif"
                       data-comment="{{@$comment->id}}" href="#">

                        {{__('frontend.Reply') }}
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
                @if(commentCanDelete($comment->id,$comment->instructor_id))
                    <a class="position_right deleteBtn" href="#"
                       data-toggle="modal"
                       onclick="deleteCommnet('{{route('deleteComment',$comment->id)}}','{{$comment->id}}_single_comment')"
                       data-target="#deleteComment">
                        <i class="fas fa-trash  fa-xs"></i>
                    </a>
                @endif
                <a href="#">
                    <h5>{{$comment->user['name']}}</h5>
                </a>
                <span>{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}  </span>


                <p>{{@$comment->comment}}</p>

            </div>
        </div>
    </div>
    <div
        class="d-none inputForm comment_box_inner comment_box_inner_reply reply_form_{{@$comment->id}}">

        <form action="{{route('submitCommnetReply')}}" method="post">
            @csrf
            <input type="hidden" name="comment_id"
                   value="{{@$comment->id}}">
            <div class="row">
                <div class="col-lg-12">
                    <div class="single_input mb_25">
                                                                                            <textarea
                                                                                                placeholder="Leave a reply"
                                                                                                rows="2" name="reply"
                                                                                                class="primary_textarea gray_input h-25"></textarea>
                    </div>
                </div>
                <div class="col-lg-12 mb_30">
                    @if ($isEnrolled)
                        <button type="submit"
                                class="theme_btn small_btn4">
                            <i class="fas fa-reply"></i>
                            {{__('frontend.Reply') }}
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
    @if(isset($comment->replies))
        @foreach ($comment->replies->where('reply_id',null) as $replay)

            <div class="comment_box_inner comment_box_inner_reply" id="{{$replay->id}}_single_comment_reply">
                <div class="comment_box_info ">

                    <div class="thumb">
                        <div
                            class="profile_info profile_img collaps_icon d-flex align-items-center">
                            <div class="studentProfileThumb"
                                 style="background-image: url('{{getProfileImage($replay->user['image']??'')}}');margin: 0"></div>

                        </div>

                    </div>

                    <div class="comment_box_text link">
                        @if ($isEnrolled)
                            <a class="position_right reply2_btn   @if(ReplyCanDelete($replay->user_id,$course->user_id)) mr_20 @endif"
                               data-reply="{{@$replay->id}}" href="#">

                                {{__('frontend.Reply') }}
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                        @if(ReplyCanDelete($replay->user_id,$course->user_id))
                            <a class="position_right" href="#"
                               data-toggle="modal"
                               onclick="deleteCommnet('{{route('deleteCommentReply',$replay->id)}}','{{$replay->id}}_single_comment_reply')"
                               data-target="#deleteComment">
                                <i class="fas fa-trash  fa-xs"></i>
                            </a>
                        @endif
                        <a href="#">
                            <h5>{{@$replay->user['name']}}</h5>
                        </a>
                        <span>
                                                                            {{ \Carbon\Carbon::parse($replay->created_at)->diffForHumans() }}
                                                             </span>
                        <p>{{@$replay->reply}}</p>

                    </div>

                </div>
            </div>
            <div
                class="d-none inputForm comment_box_inner comment_box_inner_reply reply2_form_{{@$replay->id}}">

                <form action="{{route('submitCommnetReply')}}"
                      method="post">
                    @csrf
                    <input type="hidden" name="comment_id"
                           value="{{@$comment->id}}">
                    <input type="hidden" name="reply_id"
                           value="{{@$replay->id}}">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="single_input mb_25">
                                                                                            <textarea
                                                                                                placeholder="Leave a reply"
                                                                                                rows="2" name="reply"
                                                                                                class="primary_textarea gray_input h-25"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-12 mb_30">
                            @if ($isEnrolled)
                                <button type="submit"
                                        class="theme_btn small_btn4">
                                    <i class="fas fa-reply"></i>
                                    {{__('frontend.Reply') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>


            @foreach ($comment->replies->where('reply_id',$replay->id) as $replay)

                <div class="comment_box_inner comment_box_inner_reply2" id="{{$replay->id}}_single_comment_reply_reply">
                    <div class="comment_box_info ">
                        <div class="thumb">
                            <div
                                class="profile_info profile_img collaps_icon d-flex align-items-center">
                                <div class="studentProfileThumb"
                                     style="background-image: url('{{getProfileImage($replay->user['image']??'')}}');margin: 0"></div>

                            </div>

                        </div>
                        <div class="comment_box_text link">
                            @if(ReplyCanDelete($replay->user_id,$course->user_id))
                                <a class="position_right" href="#"
                                   data-toggle="modal"
                                   onclick="deleteCommnet('{{route('deleteCommentReply',$replay->id)}}','{{$replay->id}}_single_comment_reply_reply')"
                                   data-target="#deleteComment">
                                    <i class="fas fa-trash  fa-xs"></i>
                                </a>
                            @endif
                        </div>

                        <div class="comment_box_text ">

                            <a href="#">
                                <h5>{{@$replay->user['name']}}</h5>
                            </a>
                            <span>
                                                                                      {{ \Carbon\Carbon::parse($replay->created_at)->diffForHumans() }} </span>
                            <p>{{@$replay->reply}}</p>

                        </div>

                    </div>
                </div>
            @endforeach
        @endforeach
    @endif

</div>
