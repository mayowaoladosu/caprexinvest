@if(basicControl()->home_style === 'home_one')
    @include(template().'partials.navbar_one')
@else
    @include(template().'partials.navbar_two')
@endif
