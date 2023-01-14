@extends('backend.master')
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Student Name : {{ $user->name  }}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#">{{__('student.Students')}}</a>
                    <a href="#">{{__('student.Groups')}}</a>
                </div>
            </div>
        </div>
          <style>
        .progress-bar {
            background-color: #9734f2;
        }
    </style>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center mt-50">

                <div class="col-lg-12">
                    <input type="hidden" name="student_id" value="{{$user->id}}">
                    <div class="main-title">
                        <h3 class="mb-20">{{__('student.Enrolled Courses')}}</h3>

                    </div>

                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3 quiz_assign_table">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{__('common.ID')}}</th>
                                        <th scope="col" style="width: 50%">{{__('common.Name')}}</th>
                                        {{-- <th scope="col">{{__('common.Teacher')}}</th> --}}
                                        <th scope="col">{{__('common.Progress')}}</th>
                                        <th scope="col">{{__('student.Enroll Date')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($instance as $index => $enroll)
                                        <tr>
                                            <td scope="col">{{ $enroll->course->id }}</td>
                                            <td scope="col" style="width: 50%">
                                                <a href="{{route('course.enrolled_students',$enroll->course->id)}}">
                                                    {{ $enroll->course->title }}
                                                </a>
                                            </td>
                                            {{-- <td scope="col">{{ $enroll->course->user->name }}</td> --}}
                                            <td scope="col">


                                                <div class='progress_percent flex-fill text-right'>
                                                    <div class='progress theme_progressBar '>
                                                        <div class='progress-bar' role='progressbar' style="width: {{round($enroll->course->userTotalPercentage($enroll->user_id,$enroll->course_id))}}%" aria-valuenow='25'
                                                             aria-valuemin='0' aria-valuemax='100'></div>
                                                    </div>
                                                    <p class='font_14 f_w_400'>{{round($enroll->course->userTotalPercentage($enroll->user_id,$enroll->course_id))}}% Complete</p>
                                                </div>
                                            </td>
                                            <td scope="col">{{ \Carbon\Carbon::parse($enroll->created_at)->format('d M, Y') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        let table = $('.quiz_assign_table').DataTable({
            bLengthChange: false,
            "bDestroy": true,
            language: {
                emptyTable: "No data available in the table",
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
                    titleAttr: 'Excel',
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
                    titleAttr: 'CSV',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="far fa-file-pdf"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: 'PDF',
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
                    titleAttr: 'Print',
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


        $(document).on('change', '.allSelected', function () {

            if ($('.allSelected').is(':checked') == true) {
                table.rows().nodes().to$().find('input[type="checkbox"].select-checked').each(function () {
                    $(this).prop('checked', true);
                });
            } else {
                table.rows().nodes().to$().find('input[type="checkbox"].select-checked').each(function () {
                    $(this).prop('checked', false);
                });
            }

        });

        function formSubmit(){
            let data = [];
            table.rows().nodes().to$().find('input[type="checkbox"].select-checked').each(function () {
                if ($(this).is(':checked') == true) {
                    data.push($(this).val());
                }
            });

            $('#selectId').val(data);
            $('#form').submit()
        }
    </script>
@endpush
