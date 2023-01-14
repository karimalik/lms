<div class="row">
    @if(isset($sectionWidgets))
        @if(count($sectionWidgets['one'])!=0)
            <div class="col-lg-4">
                <div class="footer_widget">

                    <div class="footer_title">
                        <h3>{{ Settings('footer_section_one_title')  }}</h3>
                    </div>
                    <ul class="footer_links">
                        @foreach($sectionWidgets['one'] as $page)
                            @if(isset($page->frontpage->id))
                                @php
                                    $route = $page->is_static == 0 ? route('frontPage',$page->frontpage->slug) : url($page->frontpage->slug)
                                @endphp
                                <li><a href="{{ $route }}">{{$page->name}} </a></li>
                            @else
                                <li><a href="">{{$page->name}} </a></li>
                            @endif
                        @endforeach
                    </ul>

                </div>
            </div>
        @endif
        @if(count($sectionWidgets['two'])!=0)
            <div class="col-lg-4">
                <div class="footer_widget">
                    <div class="footer_title">
                        <h3>{{ Settings('footer_section_two_title')  }}</h3>
                    </div>
                    <ul class="footer_links">

                        @foreach($sectionWidgets['two'] as $key=> $page)
                            @if(isset($page->frontpage->id))
                                @php
                                    $route = $page->is_static == 0 ? route('frontPage',$page->frontpage->slug) : url($page->frontpage->slug)
                                @endphp
                                <li><a href="{{ $route }}">{{$page->name}} </a></li>
                            @else
                                <li><a href="">{{$page->name}} </a></li>
                            @endif
                        @endforeach

                    </ul>
                </div>
            </div>
        @endif
        @if(count($sectionWidgets['three'])!=0)
            <div class="col-lg-4">
                <div class="footer_widget">
                    <div class="footer_title">
                        <h3>{{ Settings('footer_section_three_title')  }}</h3>
                    </div>
                    <ul class="footer_links">
                        @foreach($sectionWidgets['three'] as $page)
                            @if(isset($page->frontpage->id))
                                @php
                                    $route = $page->is_static == 0 ? route('frontPage',$page->frontpage->slug) : url($page->frontpage->slug)
                                @endphp
                                <li><a href="{{ $route }}">{{$page->name}} </a></li>
                            @else
                                <li><a href="">{{$page->name}} </a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @endif
</div>
