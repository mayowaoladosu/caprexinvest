@extends($theme . 'layouts.app')
@section('title',trans('Home'))
@section('content')
    {!!  $sectionsData !!}
    @if(($slug ?? '/') === '/' && basicControl()->trading_enabled && basicControl()->trading_homepage_chart_enabled)
        <section class="py-5" id="live-market">
            <div class="container">
                <div class="mb-3">
                    <h3 class="mb-1">@lang('Live Market Chart')</h3>
                </div>
                <div class="card">
                    <div class="card-body" style="height: 480px;">
                        @include(template() . 'partials.trading_chart_widget', [
                            'chartSymbol' => basicControl()->trading_chart_symbol ?: 'NASDAQ:AAPL',
                            'chartInterval' => basicControl()->trading_chart_interval ?: 'D',
                            'chartTheme' => basicControl()->trading_chart_theme ?: 'dark',
                        ])
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
