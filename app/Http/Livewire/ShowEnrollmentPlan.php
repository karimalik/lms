<?php

namespace App\Http\Livewire;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Org\Entities\OrgPosition;
use Modules\OrgSubscription\Entities\OrgCourseSubscription;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Traits\WithFilters;
use Rappasoft\LaravelLivewireTables\Traits\WithSearch;
use Rappasoft\LaravelLivewireTables\Utilities\ColumnUtilities;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class ShowEnrollmentPlan extends DataTableComponent
{
    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];
    public bool $columnSelect = true;
    public bool $rememberColumnSelection = true;
    public  $table='org_course_subscriptions';
    use WithSearch;
    use WithFilters;
    use WithPagination;

    public $page = 1;
    public $plan = null;


    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function mount()
    {
        $this->plan = null;
        $this->emit('refreshDatatable');
    }


    public function columns(): array
    {
        return [
            Column::make(__('common.Title'), 'title')
                ->sortable()
                ->searchable(),


            Column::make(__('org-subscription.Join Date'), 'join_date')
                ->sortable()
                ->searchable(),

            Column::make(__('org-subscription.End Date'), 'end_date')
                ->sortable()
                ->searchable(),

            Column::make(__('org-subscription.Duration'), 'days')
                ->sortable()
                ->searchable(),

            Column::make(__('org-subscription.Plan'), 'type')
                ->sortable()
                ->searchable(),


        ];
    }

    public function query()
    {
        $query = OrgCourseSubscription::where('status', 1)->orderBy('order', 'asc');
        if (!empty($this->plan)) {
            $query->where('type', $this->plan);
        }
        return $query;
    }

    public function rowView(): string
    {
        $this->emptyMessage = trans("common.No data available in the table");
        return 'livewire.org-enrollment-plan.row';
    }

    public function paginationView()
    {
        return 'backend.partials._pagination';
    }


    public function render()
    {

        return view('livewire.org-enrollment-plan.datatable')
            ->with([
                'columns' => $this->columns(),
                'rowView' => $this->rowView(),
                'filtersView' => $this->filtersView(),
                'customFilters' => $this->filters(),
                'rows' => $this->rows,
                'modalsView' => $this->modalsView(),
                'bulkActions' => $this->bulkActions,
            ]);
    }
}
