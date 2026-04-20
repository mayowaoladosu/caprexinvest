<!-- pricing  2 -->
<section class="pricing-two">
    <div class="container">
        <div class="common-title text-center">
            <h6>@lang($pricing_section_two['single']['title']??'')</h6>
            <h3>@lang(styleSentence2($pricing_section_two['single']['sub_title']??'',3))</h3>
        </div>
        <div class="row g-4">
            @foreach($pricing_section_two['investmentPlans'] as $plan)
                <div class="col-lg-3 col-md-4 pricing-column">
                    <div class="pricing-single-two ">
                        <div class="pricing-header">
                            <div class="mb-2">{!! $plan->getAssetBadge() !!}</div>
                            <h5>@lang($plan->plan_name)</h5>
                            <h3>{{$plan->investAmount()}} <sub>( {{$plan->getPlanPeriod()}})</sub></h3>
                            <p>@lang($plan->short_description)</p>
                            <div class="card-two-shape">
                                <img src="{{asset(template(true).'images/shape/card-shape-1.png')}}" alt="shape">
                            </div>
                        </div>
                        <div class="pricing-list-two">
                            <ul>
                                {!! $plan->getDescription() !!}
                            </ul>
                        </div>
                        <a href="" class="btn-1 myBtn"
                           onclick="event.preventDefault()"
                           data-id="{{$plan->id}}"
                           data-plan="{{$plan->plan_name}}"
                           data-invest="{{$plan->investAmount()}}"
                           data-profit="{{$plan->getProfit()}}"
                           data-period="{{$plan->getReturnPeriod()}}"
                           data-return_number="{{$plan->number_of_profit_return??'Lifetime Earning'}}"

                        >
                            @lang('Get Started')
                            <i class="fa-regular fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>@lang('Invest Now')</h5>
                    <span class="close">&times;</span>
                </div>
                <div class="modal-body plan-modal">
                    <h4 id="PlanName" class="text-center"></h4>
                    <p class="price-range text-center mb-1"></p>
                    <p class="profit-details text-center mb-1"></p>
                    <p class="profit-validity text-center mb-2"></p>
                    <p class="number_of_return text-center mb-2"></p>
                    <form action="{{route('user.investPlan')}}" method="post">
                        @csrf
                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <select class="nice-select" aria-label="Default select example" name="balance_type">
                                    @auth
                                        <option
                                            value="balance">@lang('Wallet Balance -') {{currencyPosition(auth()->guard('web')->user()->balance)}}</option>
                                        <option
                                            value="profit">@lang('Profit Balance -') {{currencyPosition(auth()->guard('web')->user()->profit_balance)}}</option>
                                    @endauth
                                    <option value="checkout">@lang('Checkout')</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="text" class="form-control invest-amount" name="amount" id="amount"
                                           value="{{old('amount')}}"
                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" autocomplete="off"
                                           placeholder="@lang('Enter amount')"/>
                                    <span class="input-group-text show-currency bg-white">{{basicControl()->currency_symbol}}</span>
                                </div>
                            </div>
                            <input type="hidden" name="plan_id" class="plan-id">
                            <button type="submit" class="btn-1 d-flex justify-content-center align-items-center text-center planPurchaseSubmitButton">@lang('Make Payment') <span></span></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

</section>
<!-- pricing 2 -->
