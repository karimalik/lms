<div>
    <input type="hidden" id="url" value="{{url('/')}}">
    <div class="main_content_iner wishList_main_content">
        <div class="container-fluid">
            <div class="my_courses_wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="section__title3 margin-50">
                            <h3>{{__('frontendmanage.Notifications')}}</h3>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- @dd($all_notifications) --}}
                        <div class="table-responsive">
                            @if(Auth::user()->notifications->count()==0)
                                <div class="col-12">
                                    <div class="section__title3 margin_50">
                                        <p class="text-center">{{__('student.Empty')}}!</p>
                                    </div>
                                </div>
                            @else
                                <table class="table custom_table mb-0" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>{{__('frontendmanage.Notification')}}</th>
                                        <th>{{__('common.Date')}}</th>
                                        <th>{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($all_notifications)
                                        @foreach($all_notifications as $notification)
                                        {{-- @dd($notification) --}}
                                            @php
                                                if($notification->read_at==null){
                                                    $start_tag='<b>';
                                                    $end_tag='</b>';
                                                }else{
                                                    $start_tag='<p>';
                                                    $end_tag='</p>';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-red" style="max-width: 80%;">
                                                    @php
                                                        $text=explode('.',$notification->data['body']);
                                                    @endphp
                                                    @if ($notification->read_at==null)
                                                    <a href="#" class="unread_notification" id="{{$notification->id}}" title="Mark As Read" data-notification_id="{{$notification->id}}">
                                                           
                                                        <p class="notifi_par notify_{{$notification->id}}">
                                                           {{@$text[0]}}. 
                                                                <br>
                                                        </p> 
                                                        <p class="notifi_par notify_{{$notification->id}}"> {!! @$text[1] !!}  </p>
                                                        <p class="notifi_par notify_{{$notification->id}}"> {!! @$text[2] !!}  </p>
                                                        <p class="notifi_par notify_{{$notification->id}}"> {!! @$text[3] !!}  </p>
                                                        <p class="notifi_par notify_{{$notification->id}}"> {!! @$text[4] !!}  </p>
                                                        <p class="notifi_par notify_{{$notification->id}}"> {!! @$text[5] !!}  </p>
                                                    </a>
                                                    @else
                                                         <p>{{@$text[0]}}. </p> 
                                                            <br>
                                                        <p>{{@$text[1]}} </p>
                                                    @endif
                                                  
                                                </td>
                                                <td  style="max-width: 10%">
                                                {{showDate($notification->created_at)}}
                                                </td>
                                                <td  style="max-width: 10%">
                                                   <a target="_blank" href="{{$notification->data['actionURL']}}">{{$notification->data['actionText']}}</a>
                                                </td>
                                                
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                {{ $all_notifications->links() }}
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>