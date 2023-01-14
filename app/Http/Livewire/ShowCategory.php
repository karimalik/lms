<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Modules\CourseSetting\Entities\Category;

class ShowCategory extends Component
{
    protected $category, $categoryCode;
    public $codes = [];

    protected $listeners = ['checkCategory'];

    public function checkCategory($codes)
    {
        $this->codes = $codes;
    }

    public function categoryFilter($category_id)
    {
        $this->emit('addCategoryFilter', $category_id);
    }

    public function render()
    {
        $categories = Category::where('parent_id', null)->orderBy('name', 'asc')->get();
        return view('livewire.show-category', [
            'categories' => $categories
        ]);
    }
}
