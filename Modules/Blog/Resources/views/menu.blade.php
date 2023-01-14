<li>
    <a href="#" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <i class="fas fa-blog"></i>
        </div>
        <div class="nav_title">
            <span>{{__('common.Blogs')}}</span>
        </div>
    </a>
    <ul>

        <li>
            <a href="{{route('blog-category.index')}}"> {{__('common.Category')}}</a>
        </li>
        <li>
            <a href="{{route('blogs.index')}}"> {{__('common.Post')}}</a>
        </li>
    </ul>
</li>
