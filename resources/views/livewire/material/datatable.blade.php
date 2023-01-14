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
        .QA_section .QA_table .org_student_table .table{
            min-height: 150px;
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
                        <div class=" btn-group" wire:ignore>
                            <select class="primary_select studentPositionSelect width_200" wire:model="type"
                                    wire:change="selectType">
                                <option value="">{{__('org.Filter By Type')}}</option>
                                <option value="Video">Video</option>
                                <option value="Excel">Excel</option>
                                <option value="PPT">PPT</option>
                                <option value="Doc">Doc</option>
                                <option value="PDF">PDF</option>
                                <option value="SCORM">SCORM</option>
                            </select>

                        </div>
                    </div>
                </div>


                <div class="d-md-flex">
                    <div class=" btn-group">
                        <a class="primary-btn radius_30px mr-10 fix-gr-bg mt-10 line-height-19"
                           id="add_material_btn" href="#"><i
                                class="ti-plus"></i>{{__('org.Add Material')}}</a>

                        <a class="primary-btn radius_30px mr-10 fix-gr-bg mt-10  line-height-19"
                           href="{{route('org.material-source-export')}}"><i
                                class="ti-download"></i>{{__('org.Export Material')}}</a>
                        <div class="mr-10 fix-gr-bg mt-10  pr-3 ">


                        </div>

                    </div>
                </div>
                <div class="d-md-flex">
                    <div>

                        @include('livewire-tables::bootstrap-4.includes.column-select')
                    </div>
                </div>
                <div class="d-md-flex">
                    <div>
                        @include('livewire.partials.search')
                    </div>
                </div>

            </div>

            @include('livewire-tables::bootstrap-4.includes.table')
            @include('livewire-tables::bootstrap-4.includes.pagination')


            <input type="hidden" id="showAddBtn" value=" {{$showAddBtn?'1':'0'}}">
            <input type="hidden" id="categories" value=" {{implode("|",$categories)}}">
        </div>


    </div>
    @push('js')

        <script>
            $(document).ready(function () {
                $('.primary_select').on('change', function (e) {
                @this.set('type', e.target.value);
                @this.selectType()
                });


                $('#add_material_btn').on('click', function (e) {
                    var showAddBtn = $('#showAddBtn').val();


                    if (showAddBtn == 1) {
                        var categories = $('#categories').val();
                        $('#addCategory').val(categories)
                        $('#add_material').modal('toggle');

                    } else {
                        toastr.error('Please Select a Category', 'Failed');
                    }
                });

            });
        </script>


    @endpush

    <script>



    </script>
</div>
