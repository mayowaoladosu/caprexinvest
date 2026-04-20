<div class="tradingview-widget-container">
    <div class="tradingview-widget-container__widget"></div>
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
        {
            "autosize": true,
            "symbol": "{{ $chartSymbol ?? 'NASDAQ:AAPL' }}",
            "interval": "{{ $chartInterval ?? 'D' }}",
            "timezone": "Etc/UTC",
            "theme": "{{ $chartTheme ?? 'dark' }}",
            "style": "1",
            "locale": "en",
            "allow_symbol_change": true,
            "withdateranges": true,
            "hide_side_toolbar": false,
            "details": true,
            "hotlist": true,
            "calendar": false,
            "studies": [],
            "support_host": "https://www.tradingview.com"
        }
    </script>
</div>

