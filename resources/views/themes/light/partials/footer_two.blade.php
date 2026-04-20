<!-- footer -->
<footer id="footer" class="main-footer footer-two">
    <div class="bg-layer" style="background: url('{{asset(template(true).'images/background/footer-bg-2.jpg')}}');"></div>
    <div class="container">
        <div class="footer-title">
            <h3>
                {{$footer_section['single']['newsletter_title']??''}}
            </h3>
            <p>
                {{$footer_section['single']['newsletter_description']??''}}
            </p>
            <form action="{{route('subscribe')}}" method="post">
                @csrf
                <div class="footer-newsletter-form">
                    <input type="email" name="email" placeholder="@lang('Your Email')" required>
                    <button type="submit" class="btn-1">@lang('Subscribe')  <i class="fa-regular fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
        <div class="footer-widget-container">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer-widget about-widget">
                        <div class="about-widget-inner">
                            <div class="footer-logo">
                                <img src="{{logo()}}" alt="@lang('logo')">
                            </div>
                            <p>{{$footer_section['single']['short_description']??''}}</p>
                            <div class="social-media">
                                <ul>
                                    @foreach(collect($footer_section['multiple'])->toArray() as $item)
                                        <li><a href="{{$item['media']->link??''}}"><i class="{{$item['media']->icon??''}}"></i></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3  col-lg-4  col-md-6">
                    <div class="footer-widget company-widget">
                        <div class="company-widget-inner">
                            <h6 class="footer-widget-title">@lang('Useful Link')</h6>
                            <ul class="footer-widget-list">
                                @php
                                    $usefl_link = getFooterMenuData('useful_link')
                                @endphp
                                @if($usefl_link != null)

                                    @foreach($usefl_link as $list)
                                        {!! $list !!}
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4  col-md-5">
                    <div class="footer-widget contact-widget">
                        <div class="contact-widget-inner">
                            <h6 class="footer-widget-title">@lang('Contact')</h6>
                            <ul class="footer-widget-list">
                                <li><i class="fa-light fa-phone"></i><a href="tel:{{$footer_section['single']['phone']??''}}">{{$footer_section['single']['phone']??''}}</a></li>
                                <li><i class="fa-light fa-location-dot"></i><a href="" onclick="event.preventDefault()" >{{$footer_section['single']['address']??''}}</a></li>
                                <li><i class="fa-light fa-envelope"></i><a href="mailto:{{$footer_section['single']['email']??''}}">{{$footer_section['single']['email']??''}}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-7">
                    <div class="footer-widget link-widget">
                        <div class="link-widget-inner">
                            <h6 class="footer-widget-title">@lang('Support Link')</h6>
                            <ul class="footer-widget-list">
                                @php
                                    $support_link = getFooterMenuData('support_link')
                                @endphp
                                @if($support_link != null)

                                    @foreach($support_link as $list)
                                        {!! $list !!}
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            <div class="footer-copyright-content d-flex justify-content-between">
                <p> @lang('All right reserved') @lang(basicControl()->site_title) &copy; @lang(date("Y"))</p>
                <form action="{{route('language')}}" method="get" id="language_form">
                    <select class="cmn-select2 language" name="language" >
                        @foreach($languages as $language)
                            <option value="{{$language->short_name}}" @selected(session('lang') == $language->short_name)> {{$language->name}}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</footer>
<!-- footer -->

