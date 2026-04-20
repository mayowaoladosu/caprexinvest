
<!-- blog -->
<section class="blog">
    <div class="container">
        <div class="common-title-container">
            <div class="common-title">
                <h6>@lang($blog_section['single']['title']??'')</h6>
                <h3> @lang(styleSentence2($blog_section['single']['sub_title']??'',3)) </h3>
            </div>
            <div class="common-title-btn">
                <a href="{{$blog_section['single']['media']->button_url??''}}" class="btn-1">@lang($blog_section['single']['button_name']??'') <i class="fa-regular fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="row g-4">
            @foreach($blog_section['blogs'] as $blog)
                <div class="col-lg-4 col-md-6">
                    <div class="blog-single">
                        <div class="blog-single-image">
                            <a href="{{route('blog.details',$blog->slug??'blog-details')}}"><img src="{{getFile($blog->blog_image_driver,$blog->blog_image)}}" alt="image"></a>
                        </div>
                        <div class="blog-single-content">
                            <div class="blog-meta">
                                <ul>
                                    <li><i class="fa-light fa-user"></i> @lang('By Admin')</li>
                                    <li><i class="fa-light fa-calendar-days"></i> {{dateTime($blog->created_at,'F j, Y')}}</li>
                                </ul>
                            </div>
                            <a href="{{route('blog.details',$blog->slug??'blog-details')}}" class="blog-single-title">{{optional($blog->details)->title}}</a>
                            <p>{{str(strip_tags(optional($blog->details)->description))->limit(100)}}</p>
                            <a href="{{$blog_section['single']['media']->button_url??''}}" class="read-more">@lang($blog_section['single']['button_name']??'')</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- blog -->
