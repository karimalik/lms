@extends('backend.master')
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1> {{__('quiz.Quiz')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#"> {{__('quiz.Quiz')}}</a>
                    <a href="#"> {{__('quiz.Question Bank')}}</a>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-12">
            <div class="white-box mb-30">
                {{ Form::open(['class' => 'form-horizontal', 'files' => false,  'method' => 'GET','id' => 'search_group']) }}
                <div class="row">

                    <div class="col-lg-4 mt-30-md md_mb_20">
                        <label class="primary_input_label" for="category_id">{{__('common.Type')}}</label>
                        <select class="primary_select "
                                id="group" name="group">
                            <option data-display=" {{__('common.Select')}}" value=""> {{__('common.Type')}}
                            </option>
                            @foreach($groups as $g)
                                <option value="{{$g->id}}" {{$group==$g->id?'selected':''}}>{{$g->title}}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="col-lg-4 mt-100-md md_mb_20">
                        <label class="primary_input_label" for="" style="    height: 30px;"></label>
                        <button type="submit" class="primary-btn small fix-gr-bg">
                            <span class="ti-search pr-2"></span>
                            {{__('quiz.Search')}}
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-20">{{__('quiz.Question Bank List')}}</h3>
                    </div>

                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">

                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>

                                    <tr>
                                        <th>
                                            <div class="d-flex items-center">


                                                <label class="primary_checkbox d-flex  " for="questionSelectAll">
                                                    <input type="checkbox"
                                                           id="questionSelectAll"
                                                           class="common-checkbox selectAllQuizQuestion">
                                                    <span class="checkmark"></span>
                                                </label>

                                                <a href="#" id="deleteAllBtn"
                                                   style="display: none;    margin-top: -5px;"
                                                   class="primary-btn small fix-gr-bg ml-2">
                                                    <span class="ti-trash"></span>
                                                </a>
                                            </div>
                                        </th>
                                        <th>{{__('common.SL')}}</th>
                                        <th>{{__('quiz.Group')}}</th>
                                        <th>{{__('quiz.Category')}}</th>
                                        <th>{{__('quiz.Question')}}</th>
                                        <th>{{__('common.Type')}}</th>
                                        <th>{{__('quiz.Image')}}</th>
                                        <th>{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>


                                    <tbody>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade admin-query" id="deleteBank">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('common.Delete')}} </h4>
                    <button type="button" class="close" data-dismiss="modal"><i
                            class="ti-close "></i></button>
                </div>

                <div class="modal-body">
                    <form action="{{route('question-bank-delete')}}" method="post">
                        @csrf

                        <div class="text-center">

                            <h4>{{__('common.Are you sure to delete ?')}} </h4>
                        </div>
                        <input type="hidden" name="id" value="" id="classQusId">
                        <div class="mt-40 d-flex justify-content-between">
                            <button type="button" class="primary-btn tr-bg"
                                    data-dismiss="modal">{{__('common.Cancel')}}</button>

                            <button class="primary-btn fix-gr-bg"
                                    type="submit">{{__('common.Delete')}}</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade admin-query" id="deleteAllBank">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('common.Delete')}} </h4>
                    <button type="button" class="close" data-dismiss="modal"><i
                            class="ti-close "></i></button>
                </div>

                <div class="modal-body">
                    <form action="{{route('question-bank-bulk-delete')}}" method="post">
                        @csrf

                        <div class="text-center">
                            <h4>{{__('common.Are you sure to delete ?')}} </h4>
                        </div>
                        <input type="hidden" name="questions" value="" id="qusList">
                        <div class="mt-40 d-flex justify-content-between">
                            <button type="button" class="primary-btn tr-bg"
                                    data-dismiss="modal">{{__('common.Cancel')}}</button>

                            <button class="primary-btn fix-gr-bg"
                                    type="submit">{{__('common.Delete')}}</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $("#lms_table").on("change", ".question", function () {
            qusIsCheck();
        });

        function qusIsCheck() {
            if ($("#lms_table input:checkbox:checked").length > 0) {
                $('#deleteAllBtn').show();
            } else {
                $('#deleteAllBtn').hide();

            }
        }

        var questions = [];

        $('#deleteAllBtn').click(function (e) {
            e.preventDefault();
            $('#qusList').val('');

            $('#lms_table input:checkbox').each(function () {
                if (this.checked) {
                    questions.push($(this).val());
                }

            });
            $('#qusList').val(questions.toString());
            $('#deleteAllBank').modal('show');
        });
    </script>

    @php
        $url = route('getAllQuizData').'?group='.$group;
    @endphp

    <script>
        let table = $('#lms_table').DataTable({

            bLengthChange: true,
            "bDestroy": true,
            processing: true,
            serverSide: true,
            order: [[1, "desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: '{!! $url !!}',
                pages: 5 // number of pages to cache
            }),
            columns: [
                {data: 'delete_btn', name: 'delete_btn', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'id', orderable: true},

                {data: 'questionGroup_title', name: 'questionGroup.title'},
                {data: 'category_name', name: 'category.name'},
                {data: 'question', name: 'question'},
                {data: 'type', name: 'type'},
                {data: 'image', name: 'image', orderable: false},
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
            dom: 'Blfrtip',
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

            paging: true,
            "lengthChange": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]

        });


        $(document).on('click', '.deleteQuiz_bank', function () {
            let id = $(this).data('id');
            $('#classQusId').val(id);
            $("#deleteBank").modal('show');
        });


        $(document).on('click', '.selectAllQuizQuestion', function () {
            if ($(this).is(':checked') == true) {

                table.rows().nodes().to$().find('input[type="checkbox"].question').each(function () {
                    $(this).prop('checked', true);
                });
            } else {
                table.rows().nodes().to$().find('input[type="checkbox"].question').each(function () {
                    $(this).prop('checked', false);

                });
            }
            qusIsCheck();
        });
    </script>
    <script src="{{asset('/')}}/Modules/CourseSetting/Resources/assets/js/course.js"></script>
@endpush
