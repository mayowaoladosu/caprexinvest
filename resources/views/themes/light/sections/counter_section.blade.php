<!-- counter -->
<section class="counter"  data-jarallax data-speed="0.2" data-imgPosition="50% 0%">
    <div class="container">
        <div class="counter-container">
            <div class="row">
                @foreach(collect($counter_section['multiple'])->toArray() as $item)
                    <div class="col-lg-3 col-md-6">
                        <div class="counter-single">
                            <div class="counter-single-inner">
                                <div class="odometer-box">
                                    <h2 class="odometer" data-count="{{$item['media']->count??''}}">00</h2>
                                    <h2 class="odometer-text">{{$item['prefix']??''}}</h2>
                                </div>
                                <h5>{{$item['countable_item_name']??''}}</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- counter -->
