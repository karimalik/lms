<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogCategory;

class BlogSidebarSection extends Component
{
    public $tags = [], $category = '';

    public function __construct($tag = null)
    {
        if (!empty($tag)) {
            $this->tags = explode(',', $tag);
        }

        $this->category =request('category');
    }


    public function render()
    {
        $tags = $this->tags;
        $categories = BlogCategory::where('status', 1)->get();
        $latestPosts = Blog::with('category')->where('status', 1)->take(3)->latest()->get();
        return view(theme('components.blog-sidebar-section'), compact('categories', 'latestPosts', 'tags'));
    }
}
