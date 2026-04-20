<!-- about 2 -->
<section class="about-two">
    <div class="ellips-right">
        <img src="{{asset(template(true).'images/shape/ellipse-right.png')}}" alt="shape">
    </div>
    <div class="ellips-left">
        <img src="{{asset(template(true).'images/shape/ellipse-left.png')}}" alt="shape">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="about-left-container">
                    <div class="about-left-top-conent">
                        <h4><img src="{{isset($about_section_two['single']['media']->banner_icon)?getFile($about_section_two['single']['media']->banner_icon->driver,$about_section_two['single']['media']->banner_icon->path):''}}" alt="icon"> @lang($about_section_two['single']['banner_title_1']??'')</h4>
                        <p>@lang($about_section_two['single']['banner_sub_title_1']??'')</p>
                    </div>
                    <div class="about-two-left-image">
                        <img src="{{isset($about_section_two['single']['media']->banner_image)?getFile($about_section_two['single']['media']->banner_image->driver,$about_section_two['single']['media']->banner_image->path):''}}" alt="image">
                    </div>
                    <div class="about-left-bottom-content">
                        <p>@lang($about_section_two['single']['banner_title_2']??'')</p>
                        <h4>@lang($about_section_two['single']['banner_sub_title_2']??'')</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-right-container">
                    <div class="common-title">
                        <h6>@lang($about_section_two['single']['title']??'')</h6>
                        <h3>@lang(styleSentence2($about_section_two['single']['sub_title']??'',2))</h3>
                    </div>
                    <p class="about-two-description">@lang($about_section_two['single']['description']??'')</p>
                    <div class="row">
                        @foreach(collect($about_section_two['multiple'])->toArray() as $item)
                            <div class="col-lg-6 col-md-6">
                                <div class="about-right-single">
                                    <div class="about-right-icon">
                                        <i class="{{$item['media']->icon??''}}"></i>
                                    </div>
                                    <h5>@lang($item['title']??'')</h5>
                                    <p>@lang($item['short_description']??'')  </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{$about_section_two['single']['media']->button_url??''}}" class="btn-1 about-2-btn">@lang($about_section_two['single']['button']??'') <i class="fa-regular fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- about 2 -->
