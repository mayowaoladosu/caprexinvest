@extends(template().'layouts.app')
@section('content')
    <!-- product details -->
    <section class="product-details">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="product-details-left-container">
                        <div class="swiper projectSwiper2">
                            <div class="swiper-wrapper">
                                @foreach($product->images as $image)
                                    <div class="swiper-slide">
                                        <div class="image-container">
                                            <img src="{{getFile($image->driver,$image->image)}}" class="zoom-image"
                                                alt="@lang('image')">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper projectSwiper">
                            <div class="swiper-wrapper">
                                @foreach($product->images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{getFile($image->driver,$image->image)}}" alt="image">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="product-details-right-container">
                        <div class="product-details-right-content">
                            <div class="product-details-right-title">
                                <h4>{{optional($product->details)->title}}</h4>
                                <div class="product-details-right-rating">
                                    <ul>
                                        {!! $avg_rating !!}
                                    </ul>
                                    <h6>| {{count($reviews??[])}} @lang('Customer Reviews')</h6>
                                </div>
                                <div class="product-details-info product-details-cmn-content">
                                    <h6>@lang('Availability'): <span>{{$product->status}}</span></h6>
                                </div>
                                <div class="product-details-price">
                                    <h3>{{currencyPosition($product->price+0)}}</h3>

                                </div>
                                <div class="product-description">
                                    <p>{{strip_tags(optional($product->details)->short_description)}}</p>
                                </div>
                            </div>
                            <div class="product-details-quantity product-details-cmn-content">
                                <div class="incriment-dicriment">
                                    <div class="count-single">
                                        <button type="button" class="decrement"><i class="fa-light fa-minus"></i>
                                        </button>
                                        <span class="number product_quantity">1</span>
                                        <button type="button" class="increment"><i class="fa-light fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="product-details-quantity-inner">
                                    <div class="cart-btn">
                                        <button  class="btn-1 mt-1 addToCart" data-single="true" data-ordernow="true"><span class="button-text">@lang('Buy Now')</span> <i
                                                class="fa-regular fa-arrow-right"></i></button>
                                    </div>
                                    <div class="buy-btn">
                                        <button class="btn-1 addToCart btn-with-loader" data-single="true"> <span class="button-text"> @lang('Add to Cart') </span> <i
                                                class="fa-regular fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- product details -->


    <!-- product details info -->
    <section class="product-info-section">
        <div class="container">
            <div class="product-info-box">
                <div class="product-info-description">
                    <h3>@lang('Description')</h3>
                    @lang(optional($product->details)->description)
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <div class="product-info-box">
                        <div class="adition-info">
                            <h3>@lang('Additional Information')</h3>
                        </div>
                        <div class="adition-info-table">
                            <ul>
                                <li>
                                    <h6>@lang('Material')</h6>
                                    <p>{{$product->material}}</p>
                                </li>
                                <li>
                                    <h6>@lang('Karat')</h6>
                                    <p>{{$product->karat+0??'N/A'}} {{$product->karat?'Karat':''}}</p>
                                </li>
                                <li>
                                    <h6>@lang('Weight')</h6>
                                    <p>{{$product->quantity}} @lang('gm')</p>
                                </li>
                                <li>
                                    <h6>@lang('Category')</h6>
                                    <p>{{optional($product->category)->name??'N/A'}}</p>
                                </li>
                                <li>
                                    <h6>@lang('Subcategory')</h6>
                                    <p>@lang(optional($product->subcategory)->name)</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">

                        <div class="col-lg-6 col-md-12 mt-4">
                            <div class="product-details-right-rating">
                                <ul>
                                    {!! $avg_rating !!}
                                </ul>
                                <h6>| {{count($reviews??[])}} @lang('Customer Reviews')</h6>
                            </div>
                            <div class="review_box">
                                @forelse($reviews as $review)
                                    <div class="review-content">
                                        <div class="reviwe-image">
                                            <img src="{{ getFile(optional($review->user)->image_driver, optional($review->user)->image) }}" alt="image">
                                        </div>
                                        <div class="review-item">
                                            <h6>@lang(optional($review->user)->firstname) @lang(optional($review->user)->lastname)</h6>
                                            <div class="review-icon-list">
                                                <ul>
                                                    {!! $review->stars !!}
                                                </ul>
                                            </div>
                                            <div class="review-comments">
                                                <p>{!! $review->comment->comment !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                   <div class="empty-review-image h-100 d-flex justify-content-center align-items-center">
                                       <img src="{{asset(template(true).'images/empty_review.jpg')}}"/>
                                   </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 mt-4">
                            <div class="product-info-box mt-5">
                                <div class="contact-form">
                                    <form action="{{route('user.addRating')}}" method="post">
                                        @csrf
                                        <input type="hidden" value="{{$product->id}}" name="product_id">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="review">
                                                    <p>@lang('Your Rating')</p>
                                                    <div class="review-border"></div>
                                                    <div class="product-details-right-rating">
                                                        <div class="rating_icon d-flex align-items-center ml_10 mb-1" id="half" ></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="cmn-input" name="name" placeholder="@lang('Enter Name')">
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="email" class="cmn-input" name="email" placeholder="@lang('Your Email')">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <textarea class="cmn-input" name="massage" placeholder="@lang('Write your message')"></textarea>
                                                <button type="submit" class="btn-1">@lang('Submit Now') <i class="icon-arrow-1"></i>
                                                </button>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
    <!-- product details info -->


    <!-- related product -->
    <section class="related-product">
        <div class="container">
            <div class="common-title text-center">
                <h3>@lang('Related') <span class="star-shape"> @lang('Products') <img
                            src="{{asset(template(true) . 'images/shape/star-shape.png') }}" alt="shape"></span></h3>
            </div>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="shop-single wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1000ms">
                            <div class="shop-image-container">
                                <div class="shop-image">
                                    <a href="{{route('product.details',$relatedProduct->slug??'slug')}}"><img
                                            src="{{getFile($relatedProduct->driver,$relatedProduct->thumbnail_image)}}"
                                            alt="product"></a>
                                </div>
                                <div class="shop-icon-list">
                                    <ul>
                                        <li><a href="javascript:void(0);" class="addToWishlist {{auth()->user()?wishlist($relatedProduct->wishlist)?'addedWishlist':'':''}}"><i class="fa-light fa-heart"></i></a></li>
                                        <li><a href="{{route('product.details',$relatedProduct->slug??'slug')}}" class="quick-view-btn"><i
                                                    class="fa-light fa-eye"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="product-single-content">
                                <a href="{{route('product.details',$relatedProduct->slug??'slug')}}">{{optional($relatedProduct->details)->title}}</a>
                                <p>{{optional($relatedProduct->category)->name}}</p>
                                <div class="product-single-button">
                                    <h5>{{currencyPosition($relatedProduct->price+0)}}</h5>
                                    <button  class="btn-1 addToCart btn-with-loader" data-id="{{$relatedProduct->id}}"> <span class="button-text"> @lang('Add to bag') </span> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- related product -->
@endsection
@push('js-lib')
    <script src="{{asset($themeTrue.'js/jquery.raty.js')}}"></script>
@endpush

@push('script')
    @if(count($errors) > 0 )
        <script>
            @foreach($errors->all() as $key => $error)
            Notiflix.Notify.failure("@lang($error)");
            @endforeach
        </script>
    @endif

    <script>
       document.addEventListener('DOMContentLoaded',()=>{
           let id = "{{$product->id}}"
           let url = '{{route('addToCart')}}';

           Array.from(document.getElementsByClassName('addToCart')).forEach((item) => {

               item.addEventListener('click', async () => {
                   item.classList.add('loading');
                   item.disabled = true;
                   item.querySelector('.button-text').innerText = 'Adding...';

                   let product_id = item.dataset.id;

                   let quantity = 1;
                   if(Boolean(item.dataset.single) === true){
                       let productQuantity = document.getElementsByClassName('product_quantity')[0].textContent;
                        quantity = Number(productQuantity);
                        product_id = id;
                   }

                   try {
                       await addToCart(product_id, quantity, url);
                       item.querySelector('.button-text').innerText = 'Added!';
                   } catch (error) {
                       console.error('Error adding to cart:', error);
                       item.querySelector('.button-text').innerText = 'Error';
                   } finally {
                       setTimeout(() => {
                           item.classList.remove('loading');
                           item.disabled = false;
                           if(Boolean(item.dataset.ordernow) === true){
                               window.location.href = '{{route('checkout')}}'
                           }else {
                               item.querySelector('.button-text').innerText = 'Add To Bag';
                               $('#cart-offcanvas').addClass('popup-visible');
                           }

                       }, 1000);

                   }
               });
           });
       })

       $(document).ready(function (){
           $('#half').raty({
               half:  true,
               hints: [['bad 1/2', 'bad'], ['poor 1/2', 'poor'], ['regular 1/2', 'regular'], ['good 1/2', 'good'], ['gorgeous 1/2', 'gorgeous']],
               starHalf: '{{asset($themeTrue.'images/star-half.png')}}',
               starOff: '{{asset($themeTrue.'images/star-off.png')}}',
               starOn: '{{asset($themeTrue.'images/star-on.png')}}',
               scoreName: 'rating',

           });
       })
    </script>
@endpush
