<!-- about -->
<section class="about">
    <div class="bg-layer" style="background: url({{asset(template(true).'images/background/about-bg.jpg')}});"></div>
    <div class="container">
        <div class="row g-4 g-xl-5">
            <div class="col-lg-6">
                <div class="about-right-container">
                    <div class="common-title text-center">
                        <h6>{{$about_section['single']['title']??''}}</h6>
                        <h3>{!! styleSentence2($about_section['single']['sub_title']??'',3) !!}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-right-container">
                    <div class="about-description text-center">
                        <p>
                            {{$about_section['single']['description']??''}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- about -->
