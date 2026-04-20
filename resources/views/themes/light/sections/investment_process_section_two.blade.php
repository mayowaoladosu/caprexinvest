<!-- step -->
<section class="step" >
    <div class="bg-layer"  data-jarallax data-speed="0.2" data-imgPosition="50% 0%" style="background: url({{asset(template(true).'images/banner/banner-4-bg.jpg')}});"></div>
    <div class="container">
        <div class="common-title text-center">
            <h6>@lang($investment_process_section_two['single']['title']??'')</h6>
            <h3>@lang(styleSentence2($investment_process_section_two['single']['sub_title']??'',4,true)) </h3>
        </div>
        <div class="row">
            @foreach(collect($investment_process_section_two['multiple'])->toArray() as $key => $item)
                <div class="col-lg-3 col-md-6">
                    <div class="step-single">
                        <div class="step-single-number"> {{$key +1}} </div>
                        <a href="" onclick="event.preventDefault()">{{$item['title']??''}}</a>
                        <p>{{$item['short_description']??''}}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- step -->
