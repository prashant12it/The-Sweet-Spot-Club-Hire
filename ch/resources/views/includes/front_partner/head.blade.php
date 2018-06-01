
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Golf Club Hire -  {{ $title or 'Making golf travel simple' }}</title>
    <!-- Bootstrap -->
    <link href="{{ URL::asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{URL::asset('theme/assets/skin/default_skin/css/bootstrapdatepicker.css')}}" rel="stylesheet">
    <!-- style main css file -->
    <link href="{{ URL::asset('frontend/css/style.css') }}" rel="stylesheet">
    <!-- responsive style -->
    <link href="{{ URL::asset('frontend/css/responsive.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('frontend/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Animation file -->
    <link href="{{ URL::asset('frontend/css/animate.css') }}" rel="stylesheet">
    <!-- Custom css written by Prashant Singh -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('theme/assets/skin/default_skin/css/prashant.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('theme/assets/skin/default_skin/css/inderjeet.css') }}">
    <!-- web page text font file -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
    <script>
        var siteRelPath = '{{(getenv('APP_ENV') == 'local'?'/':(getenv('APP_ENV') == 'live'?'/shop/':'/'))}}';
    </script>
    <script src="{{ URL::asset('theme/vendor/jquery/jquery-1.11.1.min.js') }}"></script>
    <script src="//code.tidio.co/fzq7skemtdfdpueiowsgf5as1r33pjfj.js"></script>

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1694502390845935'); // Insert your pixel ID here.
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=1694502390845935&ev=PageView&noscript=1"
        /></noscript>
    <!-- DO NOT MODIFY -->
    <!-- End Facebook Pixel Code -->


    <!-- Global Site Tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-106749553-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments)};
        gtag('js', new Date());

        gtag('config', 'UA-106749553-1');
    </script>
    <!-- Global site tag (gtag.js) - Google AdWords: 825329640 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-825329640"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'AW-825329640');
    </script>
    <!-- Event snippet for Website traffic - from display ad #1 conversion page -->
    <script>
        gtag('event', 'conversion', {'send_to': 'AW-825329640/vb15CNWyunoQ6I_GiQM'});
    </script>
    <script type="text/javascript" src="//script.crazyegg.com/pages/scripts/0071/5594.js" async="async"></script>

