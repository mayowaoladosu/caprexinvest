<!-- blog 2 -->
<section class="blog-two">
    <div class="container">
        <div class="common-title">
            <h6>@lang($blog_section_two['single']['title']??'')</h6>
            <h3>@lang(styleSentence2($blog_section_two['single']['sub_title']??'',4))</h3>
        </div>
        <div class="row">
            @foreach($blog_section_two['blogs'] as $blog)
                <div class="col-lg-4 col-md-6">
                    <div class="blog-single">
                        <div class="blog-single-image">
                            <a href="{{route('blog.details',$blog->slug??'blog-details')}}"><img src="{{getFile($blog->blog_image_driver,$blog->blog_image)}}" alt="image"></a>
                        </div>
                        <div class="blog-single-content">
                            <div class="blog-content-inner">
                                <div class="blog-meta">
                                    <ul>
                                        <li><i class="fa-light fa-user"></i> @lang('By CaprexInvest Desk')</li>
                                        <li><i class="fa-light fa-calendar-days"></i> {{dateTime($blog->created_at,'F j, Y')}}</li>
                                    </ul>
                                </div>
                                <a href="{{route('blog.details',$blog->slug??'blog-details')}}"  class="blog-single-title">{{optional($blog->details)->title}}</a>
                                <p>{{str(strip_tags(optional($blog->details)->description))->limit(100)}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- blog 2 -->
