<div>
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
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('sl')))
        <x-livewire-tables::bs4.table.cell>
            {{ ++$index*request()->input('page',1)  }}
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('title')))
        <x-livewire-tables::bs4.table.cell>
            {{$row->title}}
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('category')))
        <x-livewire-tables::bs4.table.cell>
            {{$row->fullPath}}
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('type')))
        <x-livewire-tables::bs4.table.cell>
            {{$row->type}}
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('user_id')))

        <x-livewire-tables::bs4.table.cell>
            {{$row->user->name}}
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('status')))
        <x-livewire-tables::bs4.table.cell>
            <label class="switch_toggle" for="active_checkbox{{@$row->id }}">
                <input type="checkbox" class="status_enable_disable"
                       id="active_checkbox{{@$row->id }}"
                       @if (@$row->status == 1) checked
                       @endif value="{{@$row->id }}">
                <i class="slider round"></i>
            </label>
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('created_at')))
        <x-livewire-tables::bs4.table.cell>
            {{showDate($row->created_at)}}
        </x-livewire-tables::bs4.table.cell>
    @endif
    @if (!$columnSelect || ($columnSelect && $this->isColumnSelectEnabled('action')))
        <x-livewire-tables::bs4.table.cell>
            <div class="dropdown CRM_dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenu2" data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                    {{__('common.Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right"
                     aria-labelledby="dropdownMenu2">

                    <a class="dropdown-item" href="{{asset($row->link)}}">{{__('common.View')}}</a>
                    <button data-item="{{$row}}"
                            class="editMaterial dropdown-item"
                            type="button">{{__('common.Edit')}}</button>

                    <button data-id="{{$row->id}}"
                            class="deleteMaterial dropdown-item"
                            data-lessons="{{count($row->lessons)}}"
                            type="button">{{__('common.Delete')}}</button>


                </div>
            </div>

        </x-livewire-tables::bs4.table.cell>
    @endif

</div>
