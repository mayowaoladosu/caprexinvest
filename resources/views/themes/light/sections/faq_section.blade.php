<!-- faq -->
<section class="faq">
    <div class="container">
        <div class="common-title text-center">
            <h6>{{$faq_section['single']['title']??''}}</h6>
            <h3> {!! styleSentence2($faq_section['single']['sub_title']??'',1) !!} </h3>
        </div>
        <div class="row">
            <div class="col-xl-8 offset-xl-2">
                <div class="faq-container">
                    <div class="accordion" id="accordionExample">
                        @foreach(collect($faq_section['multiple'])->toArray() as $key => $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{$key}}">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}">
                                         {{$item['question']??''}}
                                    </button>
                                </h2>
                                <div id="collapse{{$key}}" class="accordion-collapse collapse {{$key == 0 ?'show':''}}" aria-labelledby="heading{{$key}}" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p>{{strip_tags($item['answer']??'')}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- faq -->
