@if(hasEcommerce())
    <!-- calculation -->
    <section class="calculation">
        <div class="flower">
            <img src="{{asset(template(true).'images/shape/flower.png')}}" alt="shape">
        </div>
        <div class="abs">
            <img src="{{asset(template(true).'images/shape/abs-2.png')}}" alt="shape">
        </div>
        <div class="container">
            <div class="common-title-container">
                <div class="common-title">
                    <h6>{{$calculation_section['single']['title']??''}}</h6>
                    <h3> {!! styleSentence2($calculation_section['single']['sub_title'],3,true) !!} </h3>
                </div>
                <div class="common-title-description">
                    <p> {{$calculation_section['single']['short_description']??''}} </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-5">
                    <div class="calculation-left-container">
                        <div class="bg-layer" style="background: url({{isset($calculation_section['single']['media']->banner_image)?getFile($calculation_section['single']['media']->banner_image->driver,$calculation_section['single']['media']->banner_image->path):''}});"></div>
                        <div class="calculation-left-content">
                            <h4>{{$calculation_section['single']['banner_title']??''}}</h4>
                            <p> {{$calculation_section['single']['banner_description']??''}} </p>
                            <a href="{{$calculation_section['single']['media']->banner_button_link??''}}" class="btn-1"> {{$calculation_section['single']['banner_button_name']??''}} <i class="fa-regular fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="calculation-right-container">
                        <h5>{{$calculation_section['single']['calculator_title']??''}}</h5>
                        <p>{{$calculation_section['single']['calculator_short_description']??''}}</p>
                        <div class="calcilation-table">
                            <div class="calcilation-table-left">
                                <table>
                                    <tr>
                                        <th>@lang('Karat')</th>
                                        <th>@lang('Weight')</th>
                                        <th>@lang('Price(per gram)')</th>
                                        <th>@lang('Value')</th>
                                    </tr>
                                    @foreach($calculation_section['karats'] as $karat)
                                        <tr>
                                            <td>{{$karat->karat+0}}@lang('ct Gold')</td>
                                            <td class="table-input">
                                                <input type="number" data-id="{{$karat->id}}" data-price="{{$karat->price}}" class="karat_weight" value="0" min="0" >
                                                <span>@lang('g')</span>
                                            </td>
                                            <td>{{currencyPosition($karat->price+0)}}</td>
                                            <td class="subTotalValue_{{$karat->id}}">{{currencyPosition(0)}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <div class="total-payment">
                                <div class="total-payment-inner">
                                    <div class="total-payment-header">
                                        <h6>@lang('Total payment')</h6>
                                    </div>
                                    <div class="total-payment-body">
                                        <h3 class="totalValue"><span>{{basicControl()->currency_symbol}}</span>0</h3>
                                        <div class="total-payment-list">
                                            <ul>
                                                @foreach(collect($calculation_section['multiple'])->toArray() as $item)
                                                    <li><i class="fa-sharp fa-light fa-circle-check"></i> {{$item['feature']??''}} </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- calculation -->
@endif
