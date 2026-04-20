<!-- choose -->
<section class="choose">
    <div class="bg-layer"  data-jarallax data-speed="0.2" data-imgPosition="50% 0%" style="background: url({{asset(template(true).'images/background/choose-bg.jpg')}});"></div>
    <div class="ring-shape">
        <img src="{{asset(template(true).'images/shape/ring-1.png')}}" alt="shape">
    </div>
    <div class="round-shape">
        <img src="{{asset(template(true).'images/shape/round-1.png')}}" alt="shape">
    </div>
    <div class="container">
        <div class="common-title text-center">
            <h6>{{ $why_choose_us_section['single']['title']??'' }}</h6>
            <h3> {!! styleSentence2($why_choose_us_section['single']['sub_title']??'',5,true) !!} </h3>
        </div>
        <div class="row g-4">
            @foreach(collect($why_choose_us_section['multiple'])->toArray()  as $item)
                <div class="col-lg-6 col-md-6">
                    <div class="choose-single">
                        <div class="choose-single-icon">
                            <img src="{{isset($item['media']->image)?getFile($item['media']->image->driver,$item['media']->image->path):''}}" alt="icon">
                        </div>
                        <h5>@lang($item['title']??'')</h5>
                        <p>@lang($item['short_description']??'')</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- choose -->
