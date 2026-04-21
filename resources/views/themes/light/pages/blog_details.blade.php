@extends(template().'layouts.app')
@section('content')
    <!-- blog details -->
    <section class="blog-details">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="blog-details-left-container">
                        <div class="blog-details-top-image">
                            <div class="image">
                                <img src="{{getFile($blog->blog_image_driver,$blog->blog_image)}}" alt="@lang('image')">
                            </div>
                            <div class="blog-details-image-info">
                                <ul>
                                    <li><i class="fa-light fa-user"></i> @lang('By CaprexInvest Desk')</li>
                                    <li><i class="fa-light fa-calendar-days"></i> {{dateTime($blog->created_at,'F j, Y')}}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="blog-details-content">
                            <h3>@lang(optional($blog->details)->title)</h3>
                            <p>@lang(optional($blog->details)->description)</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="blog-details-right-container">
                        <div class="sidebar">
                            <div class="sidebar-content">
                                <div class="sidebar-earch">
                                    <form action="{{route('blog')}}" method="get">
                                        <input type="search" name="title" class="search" placeholder="@lang('Search')...">
                                        <div class="search-btn">
                                            <button type="submit"><i class="fa-light fa-magnifying-glass"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="sidebar-content">
                                <div class="sidebar-recent-post">
                                    <h5>@lang('Recent Post')</h5>
                                    @foreach($blogs as $recentBlog)
                                        <div class="recent-post-list">
                                            <div class="recent-post-image">
                                                <img src="{{getFile($recentBlog->blog_image_driver,$recentBlog->blog_image)}}" alt="image">
                                            </div>
                                            <div class="recent-post-info">
                                                <p><i class="fa-light fa-calendar-days"></i> {{dateTime($recentBlog->created_at,'F j, Y')}}</p>
                                                <a href="{{route('blog.details',$recentBlog->slug??'blog-details')}}">@lang(optional($recentBlog->details)->title)</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="sidebar-content">
                                <div class="sidebar-tag">
                                    <h5>@lang('Categories')</h5>
                                    <div class="tags">
                                        @foreach($categories as $category)
                                            <a href="{{route('blog',$category->id)}}">{{$category->name}}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- blog details -->
@endsection
