@if(Utility::isProductionEnvironment())
    
    @if(strcasecmp(Cms::landingCCTLDDomain(config('dns.default')), 'ph') == 0)
    
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-NG87BHV');</script>
        <!-- End Google Tag Manager -->
        
    @else
    
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-P9XJMWV');</script>
        <!-- End Google Tag Manager -->
        
    @endif
    
    <script language='JavaScript1.1' async src='//pixel.mathtag.com/event/js?mt_id=1328799&mt_adid=211124&mt_exem=&mt_excl='></script>

    @if(Session::pull('seo_signup'))
        <script language='JavaScript1.1' async src='//pixel.mathtag.com/event/js?mt_id=1328801&mt_adid=211124&mt_exem=&mt_excl='></script>
    @endif

@endif