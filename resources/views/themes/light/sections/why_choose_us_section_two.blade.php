<!-- choose 2 -->
<section class="choose-two">
    <div class="bg-layer" style="background: url({{asset(template(true).'images/background/testimonial-bg.jpg')}});"></div>
    <div class="container">
        <div class="common-title text-center">
            <h6>@lang($why_choose_us_section_two['single']['title']??'')</h6>
            <h3>@lang(styleSentence2($why_choose_us_section_two['single']['sub_title']??'',3,true))</h3>
        </div>
        <div class="row g-4">
            @foreach(collect($why_choose_us_section_two['multiple'])->toArray() as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="choose-two-single">
                        <div class="choose-single-icon">
                            <img src="{{isset($item['media']->image->driver)?getFile($item['media']->image->driver,$item['media']->image->path):''}}" alt="icon">
                        </div>
                        <h5>@lang($item['title']??'')</h5>
                        <p>@lang($item['short_description']??'')</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- choose 2 -->
