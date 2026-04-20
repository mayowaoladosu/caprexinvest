<!-- testimonial -->
<section class="testimonial">
    <div class="bg-layer" style="background: url({{asset(template(true).'images/background/testimonial-bg.jpg')}});"></div>
    <div class="container">
        <div class="common-title text-center">
            <h6> {{$testimonial_section['single']['title']??''}} </h6>
            <h3> {!! styleSentence2($testimonial_section['single']['sub_title'],1) !!} </h3>
        </div>
        <div class="testimonial-container">
            <div class="theme_carousel owl-theme owl-carousel" data-options='{"loop": true, "margin": 24, "autoheight":true, "lazyload":true, "nav": false, "dots": true, "autoplay": true, "autoplayTimeout": 8000, "smartSpeed": 1000, "responsive":{ "0" :{ "items": "1" }, "600" :{ "items" : "1" }, "768" :{ "items" : "1" } , "992":{ "items" : "1" }, "1200":{ "items" : "1" }}}'>
               @foreach(collect($testimonial_section['multiple'])->toArray() as $item)
                    <div class="testimonial-single">
                        <div class="testimonial-single-content">
                            <div class="reviewer">
                                <div class="reviewer-image">
                                    <img src="{{isset($item['media']->image)?getFile($item['media']->image->driver,$item['media']->image->path):''}}" alt="image">
                                </div>
                                <div class="reviewer-info">
                                    <div class="name">{{$item['name']??''}}</div>
                                    <div class="dasegnation">{{$item['position']??''}}</div>
                                    <div class="review">
                                        <ul>
                                            @php($rating = $item['media']->rating-5)
                                            @for($i = 0; $i < (int)$item['media']->rating; $i++)
                                                <li><i class="fa-sharp fa-solid fa-star"></i></li>
                                            @endfor
                                            @for($i = 0; $i < (int)$rating; $i++)
                                                <li><i class="fa-light fa-star"></i></li>
                                            @endfor
                                            @if(is_float($item['media']->rating+0))
                                                <li><i class="fa-solid fa-star-half-stroke"></i></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-description testimonial-description-1">
                                <p>{{strip_tags($item['review']??'')}}</p>
                            </div>
                        </div>
                    </div>
               @endforeach
            </div>
        </div>
    </div>
</section>
<!-- testimonial -->
