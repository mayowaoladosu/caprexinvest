<!-- counter 2 -->
<section class="counter-two" data-jarallax data-speed="0.2" data-imgPosition="50% 0%">
    <div class="round-shape">
        <img src="{{asset(template(true).'images/shape/round-1.png')}}" alt="shape">
    </div>
    <div class="container">
        <div class="common-title text-center m-auto">
            <h6>@lang($counter_section_two['single']['title']??'')</h6>
            <h3>@lang(styleSentence2($counter_section_two['single']['sub_title']??'',2)) </h3>
        </div>
        <div class="row">
            @foreach(collect($counter_section_two['multiple'])->toArray() as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="counter-single">
                        <div class="counter-single-inner">
                            <div class="odometer-box">
                                <h2 class="odometer" data-count="{{$item['media']->count}}">00</h2>
                                <h2 class="odometer-text">{{$item['prefix']??''}}</h2>
                            </div>
                            <h5>{{$item['countable_item_name']??''}}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- counter 2 -->
