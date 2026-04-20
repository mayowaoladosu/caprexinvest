<!-- terms & condition -->
<section class="terms pt_120 pb_80">
    <div class="container">
        <div class="terms-container">
            @foreach(collect($cookie_policy['multiple'])->toArray() as $item)
                <div class="terms-content mb_40">
                    <h5 >@lang($item['heading']??'')</h5>
                    <p>
                        @lang($item['description']??'')
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- terms & condition -->
