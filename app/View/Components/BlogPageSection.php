<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogCategory;

class BlogPageSection extends Component
{
    public function render()
    {
        $query = Blog::where('status', 1)->with('user');
        if (request('query')) {
            $query->where('title', 'LIKE', "%" . request('query') . "%");
        }

        if (request('category')) {
            $categories = explode(',', request('category'));
            foreach ($categories as $key => $cat) {
                $category = BlogCategory::find($cat);

                if ($category) {
                    $ids = $category->getAllChildIds($category);
                    $ids[count($ids)] = $cat;
                    if ($key==0){
                        $query->whereIn('category_id', $ids);
                    }else{
                        $query->orWhereIn('category_id', $ids);
                    }
                }
            }

        }

        $blogs = $query->orderBy('id', 'asc')->paginate(10);
        return view(theme('components.blog-page-section'), compact('blogs'));
    }
}
