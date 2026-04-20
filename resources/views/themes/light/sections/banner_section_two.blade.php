<!-- banner-section -->
<section class="banner-section-two banner-section p-0">

    <div class="swiper-container banner-slider-1">
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <div class="bg-layer" style="background-image: url('{{asset(template(true).'images/banner/banner-4-bg.jpg')}}');"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="banner-left-container">
                                <div class="banner-left-inner">
                                    <div class="banner-content-box">
                                        <h2> {!! styleSentence($banner_section_two['single']['title']??'',4,6,true) !!} </h2>
                                        <p>
                                            {{$banner_section_two['single']['short_description']??''}}
                                        </p>
                                    </div>
                                    <div class="banner-btn-box">
                                        <div class="banner-btn">
                                            <a href="{{$banner_section_two['single']['media']->button_url??''}}" class="btn-1">{{$banner_section_two['single']['button']??''}} <i class="fa-regular fa-arrow-right"></i></a>
                                        </div>
                                        <div class="banner-video-btn">
                                            <a href="{{$banner_section_two['single']['media']->video_url??''}}" class="hv-popup-link">
                                                <i class="fas fa-play"></i>
                                            </a>
                                            <h6>{{$banner_section_two['single']['video_title']??''}}</h6>
                                        </div>
                                    </div>

                                    <div class="customer-box">
                                        <div class="customer-list">
                                            <ul>
                                                @foreach(collect($banner_section_two['multiple'])->toArray() as $item)
                                                    <li><a href="javascript:void(0);"><img src="{{isset($item['media']->reviewer)?getFile($item['media']->reviewer->driver,$item['media']->reviewer->path):''}}" alt="image"></a></li>
                                                @endforeach
                                                <li><a href="javascript:void(0);"><i class="fa-sharp fa-light fa-plus"></i></a></li>
                                            </ul>
                                        </div>
                                        <div class="customer-reviews">
                                            <h6>{{$banner_section_two['single']['review_title']??''}}</h6>
                                            <p><strong><i class="fa-sharp fa-solid fa-star"></i></strong> {{$banner_section_two['single']['review_text']??''}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="banner-two-right-conteiner">
                                <div class="banner-two-right-image">
                                    <img src="{{isset($banner_section_two['single']['media']->image)?getFile($banner_section_two['single']['media']->image->driver,$banner_section_two['single']['media']->image->path):''}}" alt="image">
                                </div>
                                <div class="banner-right-gold-image">
                                    <img src="{{asset(template(true).'images/banner/banner-right-gold.png')}}" alt="image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="bg-layer" style="background-image: url('{{asset(template(true).'images/banner/banner-4-bg.jpg')}}');"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="banner-left-container">
                                <div class="banner-left-inner">
                                    <div class="banner-content-box">
                                        <h2> {!! styleSentence($banner_section_two['single']['title']??'',4,6,true) !!} </h2>
                                        <p>
                                            {{$banner_section_two['single']['short_description']??''}}
                                        </p>
                                    </div>
                                    <div class="banner-btn-box">
                                        <div class="banner-btn">
                                            <a href="{{$banner_section_two['single']['media']->button_url??''}}" class="btn-1">{{$banner_section_two['single']['button']??''}} <i class="fa-regular fa-arrow-right"></i></a>
                                        </div>
                                        <div class="banner-video-btn">
                                            <a href="{{$banner_section_two['single']['media']->video_url??''}}" class="hv-popup-link">
                                                <i class="fas fa-play"></i>
                                            </a>
                                            <h6>{{$banner_section_two['single']['video_title']??''}}</h6>
                                        </div>
                                    </div>

                                    <div class="customer-box">
                                        <div class="customer-list">
                                            <ul>
                                                @foreach(collect($banner_section_two['multiple'])->toArray() as $item)
                                                    <li><a href="javascript:void(0);"><img src="{{isset($item['media']->reviewer)?getFile($item['media']->reviewer->driver,$item['media']->reviewer->path):''}}" alt="image"></a></li>
                                                @endforeach
                                                <li><a href="javascript:void(0);"><i class="fa-sharp fa-light fa-plus"></i></a></li>
                                            </ul>
                                        </div>
                                        <div class="customer-reviews">
                                            <h6>{{$banner_section_two['single']['review_title']??''}}</h6>
                                            <p><strong><i class="fa-sharp fa-solid fa-star"></i></strong> {{$banner_section_two['single']['review_text']??''}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="banner-two-right-conteiner">
                                <div class="banner-two-right-image">
                                    <img src="{{isset($banner_section_two['single']['media']->image)?getFile($banner_section_two['single']['media']->image->driver,$banner_section_two['single']['media']->image->path):''}}" alt="image">
                                </div>
                                <div class="banner-right-gold-image">
                                    <img src="{{asset(template(true).'images/banner/banner-right-gold.png')}}" alt="image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- banner-section -->
