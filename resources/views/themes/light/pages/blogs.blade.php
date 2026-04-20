@extends(template().'layouts.app')
@section('content')
<!-- blog -->
<section class="blog">
    <div class="container">
        <div class="row">
            @foreach($blogs as $blog)
                <div class="col-lg-4 col-md-6">
                    <div class="blog-single">
                        <div class="blog-single-image">
                            <a href="{{route('blog.details',$blog->slug??'blog-details')}}"><img src="{{getFile($blog->blog_image_driver,$blog->blog_image)}}" alt="@lang('image')"></a>
                        </div>
                        <div class="blog-single-content">
                            <div class="blog-content-inner">
                                <div class="blog-meta">
                                    <ul>
                                        <li><i class="fa-light fa-user"></i> @lang('By Admin')</li>
                                        <li><i class="fa-light fa-calendar-days"></i> {{dateTime($blog->created_at,'F j, Y')}}</li>
                                    </ul>
                                </div>
                                <a href="{{route('blog.details',$blog->slug??'blog-details')}}"  class="blog-single-title">{{optional($blog->details)->title}}</a>
                                <p>{{str(strip_tags(optional($blog->details)->description))->limit(95)}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($blogs->nextPageUrl())
            <div class="paigination">
                <ul>
                    {{ $blogs->appends($_GET)->links(template().'partials.pagination') }}
                </ul>
            </div>
        @endif

    </div>
</section>
<!-- blog -->
@endsection
