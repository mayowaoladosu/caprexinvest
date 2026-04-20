<!-- contact -->
<section class="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="contact-form">
                    <h6>{{$contact_section['single']['title']??''}} </h6>
                    <h3>{{$contact_section['single']['sub_title']??''}}</h3>
                    <form action="{{route('sent.contact.info')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="text" class="cmn-input" name="name" value="{{old('name')}}" placeholder="@lang('Enter Name')" required>
                                @error('name')
                                <span class="d-block text-danger mb-2">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <input type="number" class="cmn-input" name="phone" value="{{old('phone')}}" placeholder="@lang('Phone Number')" required>
                                @error('phone')
                                <span class="d-block text-danger mb-2">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <input type="email" class="cmn-input" name="email" value="{{old('email')}}" placeholder="@lang('Your Email')" required>
                                @error('email')
                                <span class="d-block text-danger mb-2">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="cmn-input" name="subject" placeholder="@lang('Subject')" required>
                                @error('subject')
                                <span class="d-block text-danger mb-2">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <textarea class="cmn-input" name="message" placeholder="@lang('Write you message')" required>{{old('message')}}</textarea>
                                @error('message')
                                <span class="d-block text-danger mb-2">{{$message}}</span>
                                @enderror
                                <button type="submit" class="btn-1">@lang('Submit Now') <i class="icon-arrow-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="contact-left-container">
                    <div class="contact-right-bg">
                        <img src="{{isset($contact_section['single']['media']->background_image)?getFile($contact_section['single']['media']->background_image->driver,$contact_section['single']['media']->background_image->path):''}}" alt="image">
                    </div>
                    <div class="contact-right-image">
                        <img src="{{isset($contact_section['single']['media']->image)?getFile($contact_section['single']['media']->image->driver,$contact_section['single']['media']->image->path):''}}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- contact -->

<!-- contact details -->
<section class="contact-details">
    <div class="container">
        <div class="contact-details-container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="contact-details-content">
                        <div class="contact-details-icon">
                            <i class="fa-light fa-envelope"></i>
                        </div>
                        <div class="contact-details-info">
                            <h6>@lang('Send Email')</h6>
                            <a href="mailto:{{$contact_section['single']['email']??''}}">{{$contact_section['single']['email']??''}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-details-content">
                        <div class="contact-details-icon">
                            <i class="icon-call"></i>
                        </div>
                        <div class="contact-details-info">
                            <h6>@lang('Call Us Now')</h6>
                            <a href="tel:{{$contact_section['single']['phone']??''}}">{{$contact_section['single']['phone']??''}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-details-content">
                        <div class="contact-details-icon">
                            <i class="fa-sharp fa-light fa-location-dot"></i>
                        </div>
                        <div class="contact-details-info">
                            <h6>@lang('Location')</h6>
                            <a href="" onclick="event.preventDefault()">{{$contact_section['single']['location']??''}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="contact-details-content">
                        <div class="contact-details-icon">
                            <i class="fa-sharp fa-light fa-alarm-clock"></i>
                        </div>
                        <div class="contact-details-info">
                            <h6>@lang('Update Info')</h6>
                            <a href="" onclick="event.preventDefault()">@lang($contact_section['single']['support']??'')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- contact details -->


<!-- contact map -->
<section class="contact-map">
    <iframe src="{{$contact_section['single']['media']->location_url??''}}" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>
<!-- contact map -->
