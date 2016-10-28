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
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('dashboard') }}" title="BPJS Ketenagakerjaan">BPJS Ketenagakerjaan</a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <!-- <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li> -->
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="{{ url('+/home') }}">Plus</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Account <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php
                                    $isSSOAuthenticated = session("SSO_AUTH");
                                    $isOauthValid = session("OAUTH_VALID");
                                ?>
                                @if ($isSSOAuthenticated && $isOauthValid)
                                    <li><a href="{{ route('logout') }}">Logout</a></li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </div>
    </nav>
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