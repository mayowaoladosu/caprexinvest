@extends($theme.'layouts.app')
@section('title',trans('Products'))
@section('content')
    <!-- cart -->
    <section class="cart-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-8">
                    <h5 class="cart-section-title">{{count(session('cart')??[])}} @lang('items in your bag')</h5>
                    <div class="mk_table-outer">
                        <table class="mk_cart-table">
                            <thead class="cart-header">
                            <tr>
                                <th class="prod-column">@lang('Product Details')</th>
                                <th class="price">@lang('Price')</th>
                                <th class="quantity">@lang('Quantity')</th>
                                <th class="th-total">@lang('Total')</th>
                                <th>@lang('Remove')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(session('cart')??[] as $item)
                                <tr class="table-row-1 cart-single cart-single-1" id="cartItems{{$item['id']}}">
                                    <td class="prod-column">
                                        <div class="column-box">
                                            <div class="product-thumb">
                                                <img src="{{$item['image']}}" alt="image">
                                            </div>
                                            <div class="prod-title">
                                                <h6>{{$item['name']}} </h6>
                                                <p>@lang('Weight'): <span>{{$item['product_quantity']}}gm</span></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price">{{currencyPosition($item['price'])}}</td>
                                    <td class="qty">
                                        <div class="incriment-dicriment">
                                            <div class="count-single">
                                                <button type="button" class="decrement" data-id="{{$item['id']}}"><i
                                                        class="fa-light fa-minus"></i></button>
                                                <span class="number" id="no{{$item['id']}}">{{$item['quantity']}}</span>
                                                <button type="button" class="increment" data-id="{{$item['id']}}"><i
                                                        class="fa-light fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="sub-total">{{currencyPosition($item['price'] * $item['quantity'])}}</td>
                                    <td>
                                        <div class="remove-btn remove-btn-1 cart-clear-1 removeCartItem"
                                             data-id="{{$item['id']}}">
                                            <i class="fa-light fa-trash-can"></i>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <form id="couponForm">
                        @csrf
                    <div class="mk_othre-content">
                        <div class="mk_coupon-box">
                            <input type="text" name="code" id="coupon" placeholder="@lang('Coupon code')">
                            <button type="submit" class="btn-1">@lang('Apply Coupon')</button>
                        </div>
                        <span class="invalid-feedback d-block" id="coupon-error" role="alert"> </span>
                    </div>
                    </form>
                </div>

                <div class="col-xl-4">
                    <div class="mk_total-cart-box">
                        <h3 class="fs_24 fw_sbold lh_30 d_block">@lang('Cart Totals')</h3>
                        <ul class="list clearfix mb_30">
                            <li>@lang('Sub total'):<span
                                    class="subTotalPrice">{{currencyPosition(cartTotal(session('cart')??[]))}}</span>
                            </li>
                            <li>@lang('Vat'):<span>{{basicControl()->vat+0}}%</span></li>
                            <li>@lang('Working Cost'):<span>{{basicControl()->working_cost+0}}%</span></li>
                            <li>@lang('Discount'):<span
                                    class="discountPrice">{{currencyPosition(session('discount'))}}</span></li>
                            <li>@lang('Total'):<span
                                    class="totalPriceIncludingVat">{{ currencyPosition((cartTotal(session('cart') ?? []) + vat(cartTotal(session('cart') ?? [])) + workingCost(cartTotal(session('cart') ?? [])))   - (session('discount') ?? 0)) }}</span>
                            </li>
                        </ul>
                        <div class="checkout-btn-box">
                            <a href="{{route('checkout')}}" class="btn-1">@lang('Proceed to Checkout') </a>
                            <a href="{{route('shop')}}" class="btn-1"> @lang('Continue Shopping') </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- cart -->
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#cart-offcanvas').remove();
            $('.header-cart').remove();
        })
    </script>
@endpush
