<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Welcome' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body >
    @yield('content')

    <!-- Facebook Messenger Integration -->
    <div id="fb-root"></div>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                xfbml: true,
                version: 'v18.0'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <!-- Your Chat Plugin code -->
    <div class="fb-customerchat"
         attribution="setup_tool"
         page_id="YOUR_PAGE_ID"
         theme_color="#0084ff"
         logged_in_greeting="Hi! How can we help you with any bugs or issues?"
         logged_out_greeting="Hi! How can we help you with any bugs or issues?">
    </div>

</body>
</html>