<div>
    <input type="hidden" id="url" value="{{url('/')}}">
    <div class="main_content_iner wishList_main_content">
        <div class="container-fluid">
            <div class="my_courses_wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="section__title3 margin-50">
                            <h3>{{ __('setting.Notification Setup') }}</h3>
                        </div>
                    </div>
                    <style>
                        .unread_notification{
                            font-weight: bold;
                        }
                        .notifi_par{
                            font-weight: bold;
                        }
                        .notify_normal{
                            color: var(--system_secendory_color);
                        }
                    </style>
                    <div class="col-12">
                        {{-- @dd($all_notifications) --}}
                        <form action="{{route('update_notification_setup')}}" name="notification_setup_form" id="notification_setup_form" method="POST">

                                    @csrf
                        <div class="table-responsive">
                                <table class="table custom_table mb-0" style="width: 100%">
                                    <thead>
                                    <tr>
                                       <th scope="col">{{ __('common.Name') }}</th>
                                        <th scope="col">{{ __('common.Email') }}</th>
                                        <th scope="col">{{ __('common.Browser') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                         @foreach ($templates as $key => $setup)
                                          @php
                                                if($setup['template']->is_system==1 || $setup['template']->name==null){
                                                    continue;
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{@$setup['template']->name}}
                                                </td>
                                                <td>
                                                    <label class="primary_checkbox d-flex mr-12 " for="email_option_check_{{$setup->id}}" yes="">
                                                         <input type="checkbox"  id="email_option_check_{{$setup->id}}" name="email[{{$setup['template']->act}}]"
                                                          {{isset($user_notification_setup)? in_array($setup['template']->act,$email_ids) ? 'checked':'':'checked'}} 
                                                          value="1">
                                                          <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="primary_checkbox d-flex mr-12 " for="browser_option_check_{{$setup->id}}" yes="">
                                                         <input type="checkbox" id="browser_option_check_{{$setup->id}}" name="browser[{{$setup['template']->act}}]"
                                                         {{isset($user_notification_setup)? in_array($setup['template']->act,$browser_ids) ? 'checked':'':'checked'}} 
                                                          value="1">
                                                          <span class="checkmark"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                        </div>
                        <div class="col-12 text-center">
                                        <button
                                            class="theme_btn w-100 text-center mt_40">{{__('student.Save')}}</button>
                                    </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>