@extends('backend.master')
@section('mainContent')
    @push('scripts')
        <script>
            $(document).ready(function () {
                var table = $('.Crm_table_active3').DataTable();

                // Handle form submission event
                $('.instructor_notification_setup_form').on('submit', function (e) {
                    var form = this;
                    var params = table.$('input,select,textarea').serializeArray();

                    $.each(params, function () {
                        if (!$.contains(document, form[this.name])) {
                            $(form).append(
                                $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', this.name)
                                    .val(this.value)
                            );
                        }
                    });
                });
                $('.student_notification_setup_form').on('submit', function (e) {
                    var form = this;
                    var params = table.$('input,select,textarea').serializeArray();

                    $.each(params, function () {
                        if (!$.contains(document, form[this.name])) {
                            $(form).append(
                                $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', this.name)
                                    .val(this.value)
                            );
                        }
                    });
                });
                $('.staff_notification_setup_form').on('submit', function (e) {
                    var form = this;
                    var params = table.$('input,select,textarea').serializeArray();

                    $.each(params, function () {
                        if (!$.contains(document, form[this.name])) {
                            $(form).append(
                                $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', this.name)
                                    .val(this.value)
                            );
                        }
                    });
                });
            });
        </script>
    @endpush
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{ __('setting.Notification Setup') }} </h1>
                <div class="bc-pages">
                    <a href="{{url('/dashboard')}}">{{__('common.Dashboard')}} </a>
                    <a class="active" href="#">{{ __('setting.Notification Setup') }} </a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-between">
                <div class="col-md-12">

                    <div class="row student-details student-details_tab mt_0_sm m-0">

                        <!-- Start Sms Details -->
                        <div class="col-lg-12 p-0">
                            <ul class="nav nav-tabs no-bottom-border mt_0_sm mb-20 m-0 justify-content-start"
                                role="tablist">
                                <li class="nav-item mb-0">
                                    <a class="nav-link active" href="#group_email_sms" selectTab="G" role="tab"
                                       data-toggle="tab">{{__('quiz.Instructor')}}  </a>
                                </li>
                                <li class="nav-item mb-0">
                                    <a class="nav-link" selectTab="I" href="#indivitual_email_sms" role="tab"
                                       data-toggle="tab">{{__('quiz.Student')}}</a>
                                </li>
                                @if(isModuleActive('HumanResource'))
                                    @if ($roles->where('id',4)->first() != null)
                                        <li class="nav-item mb-0">
                                            <a class="nav-link" selectTab="S" href="#staff_section" role="tab"
                                               data-toggle="tab">{{__('attendance.Staff')}}</a>
                                        </li>
                                    @endif
                                @endif

                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <input type="hidden" name="selectTab" id="selectTab">
                                <div role="tabpanel" class="tab-pane fade active show" id="group_email_sms">

                                    <div class="QA_section QA_section_heading_custom check_box_table mt-20">
                                        <form action="{{route('UpdateUserNotificationControll')}}"
                                              name="instructor_notification_setup_form"
                                              class="instructor_notification_setup_form" method="POST">

                                            @csrf
                                            <input type="hidden" name="role_id" value="2">
                                            <div class="QA_table ">
                                                <table class="Crm_table_active3">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">{{ __('common.Name') }}</th>
                                                        <th>{{__('common.Status')}}</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    @foreach($instructor_temps as $temp)
                                                        @php
                                                            if($temp['template']->is_system==1 || empty($temp['template']->name)){
                                                                continue;
                                                            }


                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                {{$temp['template']->name}}
                                                            </td>
                                                            <td>
                                                                <label class="primary_checkbox d-flex mr-12 "
                                                                       for="email_option_check_{{$temp->id}}" yes="">
                                                                    <input type="checkbox"
                                                                           id="email_option_check_{{$temp->id}}"
                                                                           name="status[{{$temp->id}}]"
                                                                           {{$temp->status==1? 'checked':''}}
                                                                           value="1">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </td>
                                                        </tr>



                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg" type="submit"
                                                        data-toggle="tooltip" title=""><span class="ti-check"></span>
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                                <div role="tabpanel" class="tab-pane fade
                                    @if(Session::has('isStudent'))
                                @if(Session::get('isStudent'))
                                        show   active
@endif
                                @endif
                                        " id="indivitual_email_sms">

                                    <div class="QA_section QA_section_heading_custom check_box_table mt-20">
                                        <form action="{{route('UpdateUserNotificationControll')}}"
                                              name="student_notification_setup_form"
                                              class="student_notification_setup_form" method="POST">

                                            @csrf
                                            <input type="hidden" name="role_id" value="3">
                                            <div class="QA_table ">
                                                <!-- table-responsive -->
                                                <table class="Crm_table_active3">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">{{ __('common.Name') }}</th>
                                                        <th>{{__('common.Status')}}</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    @foreach($students_temps as $temp)
                                                        @php
                                                            if($temp['template']->is_system==1 || empty($temp['template']->name)){
                                                                continue;
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                {{$temp['template']->name}}
                                                            </td>
                                                            <td>
                                                                <label class="primary_checkbox d-flex mr-12 "
                                                                       for="email_option_check_{{$temp->id}}" yes="">
                                                                    <input type="checkbox"
                                                                           id="email_option_check_{{$temp->id}}"
                                                                           name="status[{{$temp->id}}]"
                                                                           {{$temp->status==1? 'checked':''}}
                                                                           value="1">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </td>
                                                        </tr>


                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg" type="submit"
                                                        data-toggle="tooltip" title=""><span class="ti-check"></span>
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if(isModuleActive('HumanResource'))
                                    <div role="tabpanel" class="tab-pane fade
                                    @if(Session::has('isStudent'))
                                    @if(Session::get('isStudent'))
                                            show   active
@endif
                                    @endif
                                            " id="staff_section">

                                        <div class="QA_section QA_section_heading_custom check_box_table mt-20">
                                            <form action="{{route('UpdateUserNotificationControll')}}"
                                                  name="staff_notification_setup_form"
                                                  class="staff_notification_setup_form"
                                                  method="POST">

                                                @csrf
                                                <input type="hidden" name="role_id" value="4">
                                                <div class="QA_table ">
                                                    <!-- table-responsive -->
                                                    <table class="Crm_table_active3">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('common.Name') }}</th>
                                                            <th>{{__('common.Status')}}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach($staffs_temps as $temp)
                                                            @php
                                                                if($temp['template']->is_system==1){
                                                                    continue;
                                                                }

                                                                if($temp['template']->name==''){
                                                                    continue;
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>     {{$temp['template']->name}}
                                                                </td>
                                                                <td>
                                                                    <label class="primary_checkbox d-flex mr-12 "
                                                                           for="email_option_check_{{$temp->id}}"
                                                                           yes="">
                                                                        <input type="checkbox"
                                                                               id="email_option_check_{{$temp->id}}"
                                                                               name="status[{{$temp->id}}]"
                                                                               {{$temp->status==1? 'checked':''}}
                                                                               value="1">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                </td>
                                                            </tr>


                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-lg-12 text-center">
                                                    <button class="primary-btn fix-gr-bg" type="submit"
                                                            data-toggle="tooltip" title=""><span
                                                                class="ti-check"></span>
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
