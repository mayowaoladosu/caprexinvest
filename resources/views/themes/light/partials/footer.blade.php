@if(basicControl()->home_style === 'home_one')
    @include(template().'partials.footer_one')
@else
    @include(template().'partials.footer_two')
@endif
