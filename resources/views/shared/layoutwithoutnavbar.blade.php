<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - BPJS Ketenagakerjaan</title>
    
    <!-- Prerequisites CSS -->
    <link href="{{ URL::asset('public/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('public/css/loading-bar.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('public/css/site.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <div style="margin-top: 3.5em;">&nbsp;</div>
    @yield('content')
    
    <!-- Prerequisites JavaScripts -->
    <script src="{{ URL::asset('public/js/jquery/jquery-3.1.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('public/js/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('public/js/angularjs/angular.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('public/js/angularjs/loading-bar.min.js') }}" type="text/javascript"></script>
    
    @yield('scripts')
</body>
</html>