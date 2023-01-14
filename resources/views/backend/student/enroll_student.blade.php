@extends('backend.master')
@php
    $table_name='course_enrolleds';
@endphp
@section('table'){{$table_name}}@stop

@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('student.Enrolled Student')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#">{{__('student.Students')}}</a>
                    <a href="#">{{__('student.Enrolled Student')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center mt-50">
                <div class="col-lg-12">
                    <div class="white_box mb_30">
                        <div class="white_box_tittle list_header">
                            <h4>{{__('student.Filter Enroll History')}}</h4>
                        </div>
                        <form action="{{route('admin.enrollFilter')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-3 col-md-3 col-lg-3">
                                    <div class="primary_input ">
                                        <label class="primary_input_label"
                                               for="courseSelect">{{__('common.Select')}} {{__('courses.Course')}}</label>
                                    </div>
                                    <select class="primary_select" name="course" id="courseSelect">
                                        <option data-display="{{__('common.Select')}} {{__('courses.Course')}}"
                                                value="">{{__('common.Select')}} {{__('courses.Course')}}</option>
                                        @foreach($courses as $course)
                                            <option
                                                    value="{{$course->id}}" {{isset($courseId)?$courseId==$course->id?'selected':'':''}}>{{@$course->title}} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-xl-3 col-md-3 col-lg-3">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for="startDate">{{__('common.Select')}} {{__('common.Start Date')}}</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="{{__('common.Date')}}"
                                                               class="primary_input_field primary-input date form-control"
                                                               id="startDate" type="text" name="start_date"
                                                               value="{{isset($start)?!empty($start)?date('m/d/Y', strtotime($start)):'':''}}"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for="admissionDate">{{__('common.Select')}} {{__('common.End Date')}}</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="{{__('common.Date')}}"
                                                               class="primary_input_field primary-input date form-control"
                                                               id="admissionDate" type="text" name="end_date"
                                                               value="{{isset($end)?!empty($end)?date('m/d/Y', strtotime($end)):'':''}}"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="admission-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xl-3 mt-30">
                                    <div class="search_course_btn text-center">
                                        <button type="submit"
                                                class="primary-btn radius_30px mr-10 fix-gr-bg">{{__('common.Filter History')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('student.Enrolled Student')}} {{__('common.List')}}</h3>

                        </div>

                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">

                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{__('common.SL')}} </th>
                                        <th scope="col">{{__('common.Image')}} </th>
                                        <th scope="col">{{__('common.Name')}} </th>
                                        <th scope="col">{{__('common.Email Address')}} </th>
                                        <th scope="col">{{__('courses.Courses')}} {{__('courses.Enrolled')}}</th>
                                        <th scope="col">{{__('courses.Enrollment')}} {{__('common.Date')}} </th>
                                        <th scope="col">{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Modal Item_Details -->
            </div>
        </div>
    </section>




    <div class="modal fade admin-query" id="confirm_refund_delete">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('common.Refund Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ti-close "></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">{{__('common.Are you sure to refund')}}?</h3>
                    <p class="text-center">
                        {{__('common.Student can not access course anymore')}}.
                    </p>
                    <p class="text-center">
                        {{__('common.But also refund money to student')}}
                    </p>
                    <div class="col-lg-12 text-center">
                        <div class="mt-40 d-flex justify-content-between">
                            <button type="button" class="primary-btn tr-bg"
                                    data-dismiss="modal">{{__('common.Cancel')}}</button>
                            <a id="refund_link" class="primary-btn semi_large2 fix-gr-bg">{{__('common.Refund')}}</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="confirm_cancel_delete">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('common.Cancel Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ti-close "></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">{{__('common.Are you sure to cancel')}}?</h3>
                    <p class="text-center">
                        {{__('common.Student can not access course anymore')}}.
                    </p>
                    <p class="text-center">
                        {{__('common.But not refund money to student')}}
                    </p>
                    <div class="col-lg-12 text-center">
                        <div class="mt-40 d-flex justify-content-between">
                            <button type="button" class="primary-btn tr-bg"
                                    data-dismiss="modal">{{__('common.Cancel')}}</button>
                            <a id="delete_link" class="primary-btn semi_large2 fix-gr-bg">{{__('common.Delete')}}</a>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')

    @php
        $url =route('admin.getEnrollLogsData').'?course='.$courseId.'&start_date='.$start.'&end_date='.$end;
    @endphp

    <script>
        function confirm_refund_modal(refund_link) {
            jQuery('#confirm_refund_delete').modal('show', {backdrop: 'static'});
            document.getElementById('refund_link').setAttribute('href', refund_link);
        }

        function confirm_cancel_modal(delete_url) {
            jQuery('#confirm_cancel_delete').modal('show', {backdrop: 'static'});
            document.getElementById('delete_link').setAttribute('href', delete_url);
        }
    </script>
    <script>
        let table = $('#lms_table').DataTable({
            bLengthChange: false,
            "bDestroy": true,
            processing: true,
            serverSide: true,
            order: [[0, "desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: '{!! $url !!}',
                pages: 5 // number of pages to cache
            }),
            columns: [
                {data: 'DT_RowIndex', name: 'id'},
                {data: 'user.image', name: 'user.image', orderable: false},
                {data: 'user.name', name: 'user.name'},
                {data: 'user.email', name: 'user.email'},
                {data: 'course.title', name: 'course.title'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false},

            ],
            language: {
                emptyTable: "{{ __("common.No data available in the table") }}",
                search: "<i class='ti-search'></i>",
                searchPlaceholder: '{{ __("common.Quick Search") }}',
                paginate: {
                    next: "<i class='ti-arrow-right'></i>",
                    previous: "<i class='ti-arrow-left'></i>"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="far fa-copy"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: '{{ __("common.Copy") }}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="far fa-file-excel"></i>',
                    titleAttr: '{{ __("common.Excel") }}',
                    title: $("#logo_title").val(),
                    margin: [10, 10, 10, 0],
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    },

                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="far fa-file-alt"></i>',
                    titleAttr: '{{ __("common.CSV") }}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="far fa-file-pdf"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: '{{ __("common.PDF") }}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    margin: [0, 0, 0, 12],
                    alignment: 'center',
                    header: true,
                    customize: function (doc) {
                        doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }

                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: '{{ __("common.Print") }}',
                    title: $("#logo_title").val(),
                    exportOptions: {
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    postfixButtons: ['colvisRestore']
                }
            ],
            columnDefs: [{
                visible: false
            }],
            responsive: true,
        });


    </script>
@endpush
