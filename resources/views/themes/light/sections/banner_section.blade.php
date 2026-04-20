<!-- banner-section -->
<section class="banner-section-one banner-section">
    <div class="bg-layer" style="background: url('./assets/themes/light/images/banner/banner-3-bg.jpg');"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="banner-left-container">
                    <div class="banner-left-inner">
                        <div class="swiper-container banner-slider-1">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="banner-content-box">
                                        <h2>{!! styleSentence($banner_section['single']['title']??'',2,7) !!}</h2>
                                        <p>
                                            {!! $banner_section['single']['description']??'' !!}
                                        </p>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="banner-content-box">
                                        <h2>{!! styleSentence($banner_section['single']['title']??'',2,7) !!}</h2>
                                        <p>
                                            {!! $banner_section['single']['description']??'' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="banner-btn">
                            <a href="{{$banner_section['single']['media']->button_url??''}}"
                               class="btn-1">{{$banner_section['single']['button']??''}} <i
                                    class="fa-regular fa-arrow-right"></i></a>
                        </div>
                        <div class="powered-by">
                            <h6>{{$banner_section['single']['heading']??''}}</h6>
                            <img
                                src="{{isset($banner_section['single']['media']->powered_by)?getFile($banner_section['single']['media']->powered_by->driver,$banner_section['single']['media']->powered_by->path):''}}"
                                alt="image">
                        </div>
                        <div class="customer-box">
                            <div class="customer-rating">
                                @if(isset($banner_section['single']['media']->number_of_ratings) && $banner_section['single']['media']->number_of_ratings)
                                    <h6>@lang('Rated')
                                        <strong>{{$banner_section['single']['media']->number_of_ratings}}</strong></h6>
                                    <ul>
                                        @for($i = 0; $i < (int)$banner_section['single']['media']->number_of_ratings; $i++)
                                            <li><i class="fa-sharp fa-solid fa-star"></i></li>
                                        @endfor
                                        @if(is_float($banner_section['single']['media']->number_of_ratings+0))
                                                <li><i class="fa-solid fa-star-half-stroke"></i></li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                            <div class="customer-list">
                                <ul>
                                    @if(isset($banner_section['single']['media']->client_image_1))
                                        <li><a href="javascript:void(0);"><img
                                                    src="{{getFile($banner_section['single']['media']->client_image_1->driver,$banner_section['single']['media']->client_image_1->path)}}"
                                                    alt="image"></a></li>
                                    @endif
                                    @if(isset($banner_section['single']['media']->client_image_2))
                                        <li><a href="javascript:void(0);"><img
                                                    src="{{getFile($banner_section['single']['media']->client_image_2->driver,$banner_section['single']['media']->client_image_2->path)}}"
                                                    alt="image"></a></li>
                                    @endif
                                        @if(isset($banner_section['single']['media']->client_image_3))
                                            <li><a href="javascript:void(0);"><img
                                                        src="{{getFile($banner_section['single']['media']->client_image_3->driver,$banner_section['single']['media']->client_image_3->path)}}"
                                                        alt="image"></a></li>
                                        @endif

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="banner-right-conteiner">

                    <div class="banner-right-image">
                        <img
                            src="{{isset($banner_section['single']['media']->image)?getFile($banner_section['single']['media']->image->driver,$banner_section['single']['media']->image->path):''}}"
                            alt="image">
                    </div>
                </div>
            </div>
        </div>

        <!-- brand -->
        <div class="brand">
            <div class="swiper-container">
                <div class="swiper five-item-carousel">
                    <div class="swiper-wrapper">
                        @foreach(collect($banner_section['multiple'])->toArray() as $item)
                            <div class="swiper-slide">
                                <div class="brand-image">
                                    <img
                                        src="{{isset($item['media']->brand)?getFile($item['media']->brand->driver,$item['media']->brand->path):''}}"
                                        alt="brand">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- banner-section -->

