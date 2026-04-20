<!-- process -->
<section class="process">
    <div class="ellips-right">
        <img src="{{asset(template(true).'images/shape/ellipse-right.png')}}" alt="shape">
    </div>
    <div class="abs">
        <img src="{{asset(template(true).'images/shape/abs-1.png')}}" alt="shape">
    </div>
    <div class="container">
        <div class="row r-4">
            <div class="col-lg-4">
                <div class="common-title">
                    <h6>{{$investment_process_section['single']['title']??''}}</h6>
                    <h3>{!! styleSentence2($investment_process_section['single']['sub_title']??'',3,true) !!}</h3>
                </div>
                <div class="process-left-image">
                    <img src="{{isset($investment_process_section['single']['media']->image)?getFile($investment_process_section['single']['media']->image->driver,$investment_process_section['single']['media']->image->path):''}}" alt="image">
                </div>
            </div>
            <div class="col-lg-8">
                <div class="process-right-container">
                    <div class="row g-4">
                        @foreach(collect($investment_process_section['multiple'])->toArray() as $key => $item)
                            <div class="col-lg-6 col-md-6">
                                <div class="process-single">
                                    <div class="process-single-numb">
                                        {{$key + 1}}
                                    </div>
                                    <div class="process-single-icon">
                                        <img src="{{isset($item['media']->image)?getFile($item['media']->image->driver,$item['media']->image->path):''}}" alt="icon">
                                    </div>
                                    <div class="process-single-content">
                                        <a href="" onclick="event.preventDefault()">{{$item['title']??''}}</a>
                                        <p>{{$item['short_description']??''}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- process -->
