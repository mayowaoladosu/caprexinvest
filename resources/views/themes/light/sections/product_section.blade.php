@if(hasEcommerce())
    <!-- product -->
    <section class="product">
        <div class="container">
            <div class="common-title-container">
                <div class="common-title">
                    <h6>@lang($product_section['single']['title']??'')</h6>
                    <h3>@lang(styleSentence2($product_section['single']['sub_title']??'',3,true))</h3>
                </div>
                <div class="common-title-description">
                    <p> @lang($product_section['single']['short_description']??'')  </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="product-left-container">
                        <div class="bg-layer" style="background: url('{{isset($product_section['single']['media']->banner_image)?getFile($product_section['single']['media']->banner_image->driver,$product_section['single']['media']->banner_image->path):''}}');"></div>
                        <div class="product-left-content">
                            <h4>@lang($product_section['single']['banner_title']??'')</h4>
                            <p>@lang($product_section['single']['banner_description']??'')</p>
                            <a href="{{$product_section['single']['media']->banner_button_link??''}}" class="btn-1">@lang($product_section['single']['banner_button_name']??'') <i class="fa-regular fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                @foreach($product_section['bestSellingProducts'] as $product)
                    <div class="col-lg-3 col-md-6">
                        <div class="product-single">
                            <div class="product-single-image">
                                <img src="{{getFile($product->driver,$product->thumbnail_image)}}" alt="image">
                            </div>
                            <div class="product-single-content">
                                <h6><a href="{{route('product.details',$product->slug)}}">{{$product->title}}</a></h6>
                                <div class="sku">
                                    <p>@lang('Category') : {{$product->category_name}}</p>
                                    <span> {{currencyPosition($product->price+0)}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- product -->
@endif
