@extends($theme.'layouts.app')
@section('title',trans('Checkout'))
@section('content')
    <!-- checkout -->
    <section class="checkout">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="billing-container">
                        <h5>@lang('Billing Details')</h5>
                        <form action="{{route('order')}}" method="post">
                            @csrf
                            <div class="billing-form">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <input type="text" name="first_name" value="{{old('first_name')}}" class="billing-input" placeholder="@lang('First Name')*" required>
                                        @error('first_name')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="last_name" value="{{old('last_name')}}" class="billing-input" placeholder="@lang('Last Name')*" required>
                                        @error('last_name')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="email" name="email" value="{{old('email')}}" class="billing-input" placeholder="@lang('Email')*" required>
                                        @error('email')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="number" name="phone" value="{{old('phone')}}" class="billing-input" placeholder="@lang('Phone')*" required>
                                        @error('phone')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="address" value="{{old('address')}}" class="billing-input" placeholder="@lang('Address')*" required>
                                        @error('address')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <select class="billing-input" name="area" id="area" required>
                                            <option  disabled>@lang('Select Area')</option>
                                            @foreach($areas as $area)
                                                <option value="{{$area->id}}">@lang($area->area_name)</option>
                                            @endforeach
                                        </select>
                                        @error('area')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="city" class="billing-input" placeholder="@lang('City/Town')*" required>
                                        @error('city')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="zip" class="billing-input" placeholder="@lang('ZIP Code')">
                                        @error('zip')
                                        <span class="d-block text-danger mt-0">{{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12">
                                        <textarea  class="billing-input" name="additional_information" placeholder="@lang('Additional Information')"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button class="btn-1">@lang('Check Out') <span></span></button>
                        </form>
                    </div>
                </div>


                <div class="col-lg-5">
                    <div class="order-summery">
                        <h5>@lang('Order Summary')</h5>
                        <div class="order-summery-bottom">
                            <ul class="list">
                                @php
                                    $cart = session('cart') ?? [];
                                    $cartTotal = cartTotal($cart);
                                    $vatAmount = vat($cartTotal);
                                    $workingCostAmount = workingCost($cartTotal);
                                    $discount = session('discount') ?? 0;

                                    $finalTotal = $cartTotal + $vatAmount + $workingCostAmount - $discount;
                                @endphp
                                <li>@lang('Sub total')<span class="subTotalPrice">{{currencyPosition(cartTotal(session('cart')??[]))}}</span></li>
                                <li>@lang('Vat')<span>{{basicControl()->vat+0}}%</span></li>
                                <li>@lang('Working Cost')<span>{{basicControl()->working_cost+0}}%</span></li>
                                <li>@lang('Delivery Charge')<span class="shippingCharge" >{{currencyPosition(0)}}</span></li>
                                <li>@lang('Discount')<span>{{currencyPosition(session('discount'))}}</span></li>
                                <li>@lang('Total') ({{basicControl()->base_currency}})<span class="TotalPrice"> {{currencyPosition($finalTotal)}} </span></li>
                            </ul>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- checkout -->
@endsection


@push('script')
    <script>
        $(document).on('change','#area',function (){
            $.ajax({
                url: "{{route('shipping.charge')}}",
                method: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: "application/json",
                data: JSON.stringify({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    area_id: $(this).val(),
                }),
                success: function (res) {
                    $('.shippingCharge').text(currencyPosition(res.charge));
                    let total = '{{$finalTotal}}';
                    $('.TotalPrice').text(currencyPosition(Number(total) + Number(res.charge)))
                },
                error: function (error) {
                    var errorMessage = error.responseJSON.errors.charge[0];

                    console.log(errorMessage)
                }
            });
        })

        $(document).ready(function (){

            $.ajax({
                url: "{{route('shipping.charge')}}",
                method: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: "application/json",
                data: JSON.stringify({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    area_id: $('#area').val(),
                }),
                success: function (res) {
                    $('.shippingCharge').text(currencyPosition(res.charge));
                    let total = '{{$finalTotal}}';

                    $('.TotalPrice').text(currencyPosition(Number(total) + Number(res.charge)))
                },
                error: function (error) {
                    var errorMessage = error.responseJSON.errors.charge[0];
                }
            });

            $('#cart-offcanvas').remove();
            $('.header-cart').remove();
        })
    </script>
@endpush
