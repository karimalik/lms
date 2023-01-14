@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | Calendar @endsection
@section('css') 
    <link rel="stylesheet" href="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/core/main.css">
    <link rel="stylesheet" href="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/daygrid/main.css">
    <link rel="stylesheet" href="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/timegrid/main.css">
    <link rel="stylesheet" href="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/list/main.css">

    <link rel="stylesheet" href="{{asset('Modules/Calendar/Resources/assets/css')}}/frontend_claendar.css">
   <style>
        .fc-event-container .fc-content .fc-title {
            padding-left: 15px;
        }
        .fc-timeGridWeek-button{
            display: none;
        }
    </style>
@endsection
@section('js') 
    <script src="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/core/main.js"></script>
    <script src="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/daygrid/main.js"></script>
    <script src="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/timegrid/main.js"></script>
    <script src="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/interaction/main.js"></script>
    <script src="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/list/main.js"></script>

    {{-- <script src="{{asset('Modules/Calendar/Resources/assets/js')}}/frontend/calender_js/activation.js"></script> --}}
    
<script>
    // fc-timeGridWeek-button
        $(document).ready(function() {
            $('.fc-timeGridWeek-button').hide();
        });
        if ($("#calendar").length > 0) {
            document.addEventListener("DOMContentLoaded", function () {
                var calendarEl = document.getElementById("calendar");
                var today_date = document.getElementById("today_date").value;
                let data=<?php echo $calendars; ?>;
                var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ["dayGrid", "timeGrid", "list", "interaction"],
                initialView: "timeGridWeek",
                header: {
                    left: "prev, title , next",
                    center: "dayGridMonth,timeGridWeek",
                    right: "title",
                },
                height: "",
                defaultDate: today_date,
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events

                events: data,
                eventClick: function (info,event) {
                    console.log(info.event.extendedProps.calendar_url);
                    let system_url=$('#system_url').val();
                    let selectedDateView=info.event.start;
                    var date = new Date(selectedDateView);
                    var date = date.toLocaleDateString();
                    var date = new Date(date);
                    let formatedDate=date.toDateString();

                    let new_end_date="";
                    let formatedend_date="";
                    if (info.event.end==null) {
                         new_end_date=info.event.start;
                    } else {
                         new_end_date=info.event.end;
                         let selectedEndDateView=new_end_date;

                        var end_date = new Date(selectedEndDateView);
                        var end_date = end_date.toLocaleDateString();
                        var end_date = new Date(end_date);
                         formatedend_date=" - "+end_date.toDateString();
                    }
                    




                    $('#calendar_title').html(info.event.title);
                    $('#calendar_date_time').html(formatedDate+formatedend_date);
                    if (info.event.extendedProps.calendar_url != null) {
                        $('#calendar_link').attr('href',info.event.extendedProps.calendar_url);
                    } else {
                        $('#calendar_link').hide();
                    }
                    $('#calendar_banner').attr("src", system_url+"/"+info.event.extendedProps.banner);
                    $('.description_text').html(info.event.extendedProps.description);

                    let host_name="";
                    if (info.event.extendedProps.course_id==null) {
                        host_name=info.event.extendedProps.event.host;
                        $('#host_title').html('Event Host');
                        $('#host_image').attr('src',system_url+'/public/demo/user/admin.jpg');
                    } else {
                        host_name=info.event.extendedProps.course.user.name;
                        $('#host_title').html(info.event.extendedProps.course.user.role.name);
                        $('#host_image').attr('src',system_url+'/'+info.event.extendedProps.course.user.image);
                    }
                    $('#host_name').html(host_name);
                    console.log(host_name);

                    var modal = $("#lms_view_modal");
                    modal.modal();
                },
                dateClick: function (date, jsEvent, view) {
                    // console.log(date);
                    // $("#lms_view_modal").modal("show");
                },
                });

                calendar.render();
            });
            }

    </script>
 
@endsection

@section('mainContent')
        <x-calendar-view />
@endsection
