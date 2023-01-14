<div :wire:key="student-list">
    <style>
        .QA_section.check_box_table .QA_table .table thead tr th:first-child, .QA_table .table tbody td:first-child {
            padding-left: 25px !important;
        }

        .QA_section.check_box_table .QA_table .table thead tr th {
            padding-left: 12px !important;
        }

        .QA_section .QA_table .table thead th {
            vertical-align: middle !important;
        }

    </style>
    <div>
        <div
            @if (is_numeric($refresh))
            wire:poll.{{ $refresh }}ms
            @elseif(is_string($refresh))
            @if ($refresh === '.keep-alive' || $refresh === 'keep-alive')
            wire:poll.keep-alive
            @elseif($refresh === '.visible' || $refresh === 'visible')
            wire:poll.visible
            @else
            wire:poll="{{ $refresh }}"
            @endif
            @endif
            class="container-fluid p-0"
        >
            <div class="d-md-flex justify-content-between mb_15">
                <div class="d-md-flex">
                    <div>
                        @include('livewire.partials.org_position_select',compact('positions'))
                    </div>
                </div>

                <div class="d-md-flex">
                    <div class=" btn-group">
                        <a class="primary-btn radius_30px mr-10 fix-gr-bg mt-10 pl-3 pr-3 pt_10 line-height-14"
                           id="add_student_btn" href="#"><i
                                class="ti-plus"></i>{{__('student.Add Student')}}</a>

                        <a class="primary-btn radius_30px mr-10 fix-gr-bg mt-10 pl-3 pr-3 pt_10 line-height-14"
                           data-toggle="modal"
                           id="import_student_btn"
                           data-target="#import_student" href="#"><i
                                class="ti-plus"></i>{{__('org.Import')}}</a>

                        <a class="primary-btn radius_30px mr-10 fix-gr-bg mt-10 pl-3 pr-3 pt_10 line-height-14Modules/Org/Resources/views/material/index.blade.php"
                           href="{{route('org.student.export')}}"><i
                                class="ti-download"></i>{{__('org.Export')}}</a>
                        <div class="mr-10 fix-gr-bg mt-10  pr-3 ">


                        </div>

                    </div>
                </div>


                <div class="d-md-flex">
                    <div>

                        @include('livewire.partials.search')
                    </div>
                </div>
                <div class="d-md-flex">
                    <div>

                        @include('livewire-tables::bootstrap-4.includes.column-select')
                    </div>
                </div>
                <div class="d-md-flex">
                    <div class="">

                        <div class="dropdown CRM_dropdown mt-10 float-right">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenu2" data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                {{trans('common.Action')}}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right"
                                 aria-labelledby="dropdownMenu2">
                                <a class="dropdown-item " id="editStudent"
                                   type="button">
                                    {{trans('common.Edit')}}
                                </a>

                                <a class="dropdown-item " id="deleteStudent"
                                   type="button">
                                    {{trans('common.Delete')}}
                                </a>

                                <a class="dropdown-item " id="moveTo"
                                   type="button">
                                    {{trans('common.Move to')}}
                                </a>

                                <a class="dropdown-item " id="changeStatus"
                                   type="button">
                                    {{trans('common.Change Status')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('livewire-tables::bootstrap-4.includes.table')
            @include('livewire-tables::bootstrap-4.includes.pagination')

            <input type="hidden" id="showAddBtn" value=" {{$showAddBtn?'1':'0'}}">
            <input type="hidden" id="org_chart" value=" {{$org_chart}}">
            <input type="hidden" id="selectedRow" value=" {{$this->selectedRowsQuery->count()}}">
            @if($this->selectedRowsQuery->count()!=0)

                @foreach($this->selectedRowsQuery->get() as $row)
                    <input type="hidden" name="selectedRowsId[]" value="{{$row->id}}">
                @endforeach
            @endif

        </div>

        @php
            //@dump()
        @endphp
        {{--        @dump($this->selectedRowsQuery->get(['id']))--}}
    </div>
    @push('js')
        <script>
            $(document).ready(function () {
                $('.primary_select').on('change', function (e) {

                @this.set('pos', e.target.value);
                @this.selectPosition()
                });


                $('#add_student_btn').on('click', function (e) {
                    var showAddBtn = $('#showAddBtn').val();

                    if (showAddBtn == 1) {
                        var org_chart = $('#org_chart').val();
                        $('#addBranch').val(org_chart)

                        var position = $(".studentPositionSelect option:selected").val();
                        $("#addPositionOption").val(position);
                        $("#addPositionOption").niceSelect('update')
                        $('#add_student').modal('toggle');

                    } else {
                        toastr.error('Please Select a org chart', 'Failed');
                    }
                });

            });
        </script>
    @endpush

    <script>

        window.onload = function () {
            Livewire.on('checkOrgChart', () => {
                $('.preloader').fadeOut();
            })
        }

    </script>
</div>
