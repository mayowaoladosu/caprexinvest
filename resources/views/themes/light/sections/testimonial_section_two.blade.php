<!-- testimonial 2 -->
<section class="testimonial-two">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="common-title">
                    <h6>@lang($testimonial_section_two['single']['title']??'')</h6>
                    <h3>@lang(styleSentence2($testimonial_section_two['single']['sub_title']??'',2)) </h3>
                </div>
                <div class="testimonial-two-description">
                    <p> @lang($testimonial_section_two['single']['short_description']??'') </p>
                </div>
            </div>
            <div class="col-xl-7 offset-xl-1 col-lg-8">
                <div class="testimonial-two-container">
                    <div class="theme_carousel owl-theme owl-carousel" data-options='{"loop": true, "margin": 24, "autoheight":true, "lazyload":true, "nav": false, "dots": false, "autoplay": true, "autoplayTimeout": 8000, "smartSpeed": 1000, "responsive":{ "0" :{ "items": "1" }, "600" :{ "items" : "1" }, "768" :{ "items" : "2" } , "992":{ "items" : "2" }, "1200":{ "items" : "2" }}}'>

                        @foreach(collect($testimonial_section_two['multiple']) as $item)
                            <div class="testimonial-two-single">
                                <div class="client-info-box">
                                    <div class="client-image">
                                        <img src="{{isset($item['media']->image)?getFile($item['media']->image->driver,$item['media']->image->path):''}}" alt="image">
                                    </div>
                                    <div class="client-info">
                                        <h5>{{$item['name']??''}} </h5>
                                        <h6>{{$item['position']??''}}</h6>
                                    </div>
                                </div>
                                <p class="testimonial-description">@lang(strip_tags($item['review']??''))</p>
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
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- testimonial 2 -->
