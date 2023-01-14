<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Modules\CourseSetting\Entities\Category;
use Modules\Org\Entities\OrgBranch;
use Modules\Org\Entities\OrgMaterial;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class  ShowMaterial extends DataTableComponent
{
    use WithPagination;
    public bool $columnSelect = true;
    public bool $rememberColumnSelection = true;
    protected $listeners = ['addCategoryFilter', 'selectTypeFilter', 'checkCategory'];

    public $showAddBtn = false, $categories = [];


    public $page = 1;

    protected $materials = [];
    public $categoriesIds = [];
    public $type = null;


    public function selectType()
    {
        $this->emit('selectTypeFilter', $this->type);
    }

    public function checkCategory($codes)
    {
        if (count($codes) != 0) {
            $this->categories = $codes;
            $this->showAddBtn = true;

        } else {
            $this->showAddBtn = false;

        }
    }

    public function addCategoryFilter($branchCode)
    {
//        if (($key = array_search($branchCode, $this->categoriesIds)) !== false) {
//            unset($this->categoriesIds[$key]);
//        } else {
//            array_push($this->categoriesIds, $branchCode);
//        }



        if (($key = array_search($branchCode, $this->categoriesIds)) !== false) {
            unset($this->categoriesIds[$key]);
            $branch = Category::where('id', $branchCode)->first();
            $childs = $branch->getAllChildIds($branch);
            foreach ($childs as $child){
                if(($key2 = array_search($child, $this->categoriesIds)) !== false){
                    unset($this->categoriesIds[$key2]);
                }
            }
        } else {
            array_push($this->categoriesIds, $branchCode);
        }

        $this->emit('checkCategory', $this->categoriesIds);

    }

    public function selectTypeFilter($type)
    {
        $this->type = $type;

    }

    public function columns(): array
    {
        return [
            Column::make(__('common.SL'),'sl'),
            Column::make(__('common.Title'),'title')
                ->sortable()
                ->searchable(),
            Column::make(__('common.Category'),'category')
                ->sortable()
                ->searchable(),
            Column::make(__('common.Type'),'type')
                ->sortable()
                ->searchable(),
            Column::make(__('org.Create By'), 'user_id')
                ->sortable()
                ->searchable(),
            Column::make(__('common.Status'),'status'),
            Column::make(__('org.Create Date'), 'created_at')
                ->sortable()
                ->searchable(),
            Column::make(__('common.Action'),'action'),
        ];
    }

    public function query()
    {

        $query = OrgMaterial::with('user', 'cat');
        if (Auth::user()->role_id != 1) {
            $query->where('user_id', Auth::user()->id);
        }
        if (count($this->categoriesIds) != 0) {
            foreach ($this->categoriesIds as $key => $code) {
                $category = Category::find($code);
                if ($category) {
                    $ids = $category->getAllChildIds($category, [$code]);
                    if ($key == 0) {
                        $query->whereIn('category',  $ids);
                    } else {
                        $query->orWhereIn('category', $ids);
                    }
                }

            }
        }

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }
        return $query;
    }

    public function rowView(): string
    {
        $this->emptyMessage = trans("common.No data available in the table");
        return 'livewire.show-material';
    }

    public function paginationView()
    {
        return 'backend.partials._pagination';
    }

    public function render()
    {
        return view('livewire.material.datatable')
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
