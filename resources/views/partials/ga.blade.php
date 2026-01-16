@php($ga = config('services.ga4.measurement_id'))

@if(app()->environment('production') && $ga)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga }}');
    </script>
@endif
