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
	<link href="{{ URL::asset('public/css/dashboard.css') }}" rel="stylesheet">
    @yield('styles')
	
<style>


#circle-custom {
	border-radius:50%;
	text-align:right;
}

#circle-custom span {
	background:green;
	border-radius:50%;
	padding:15px;
}

</style>	
	
</head>
<body>

	<div class="container">
		<div class="row">
			<!--
			-->
			<div id="headerdashboard">
				<div class="row">
					<div class="col-md-3" id="logos">
						<img src="public/img/bpjstk_logo600x140.png" class="img-responsive">
					</div>
					<div class="col-md-6 col-xs-12 pull-right" style="background-color:;">					
						<div class="col-md-4 text-right col-xs-12" id="" style="background-color:;margin-top:30px;">
							<div id="circle-custom">
								<span>test</span>
							</div>
						</div>
						<div class="col-md-3 col-xs-12" style="background-color:;padding-left:0px;margin-top:30px;text-align:right;">
							<i id="clrhello">Hello</i>, <b id="unamecolor">{{ session('loggedinusername') }}</b>
						</div>
						<div class="col-md-3 col-xs-12 text-right" id="logot" style="margin-top:15px;" >
							<a href="#" id="linkini"><b>LOGOUT</b></a>
						</div>
						<!--
						-->
					</div>

				</div>
			</div>

				<!--
				<div class="col-md-3 col-sm-2" id="uname" style="background-color:;">
					<div class="col-md-6 text-left" style="padding-left:0px;">
						<i id="clrhello">Hello</i>, <b id="unamecolor">{{ session('loggedinusername') }}</b>
					</div>
					<div class="col-md-2" style="">
						<a href="#" id="linkini"><b>LOGOUT</b></a>
					</div>
				</div>
				
				<div class="col-md-2 text-right" id="logot" >
					<a href="#" id="linkini"><b>LOGOUT</b></a>
				</div>
				
				-->
			
		  <!--
  <nav class="navbar navbar-default" style="margin-bottom:0%;border-radius:0px;padding-right:0px;">
    <div class="container-fluid" style="background-color:;height:90px;">
      <div class="navbar-header" style="background-color:;padding-left:2%;padding-top:1%;">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="http://disputebills.com">
			<img src="public/img/bpjstk_logo600x140.png" class="img-responsive">
        </a>
      </div>
      <div id="navbar1" class="navbar-collapse collapse" style="">
		<ul class="nav navbar-nav navbar-right" style="margin-right:1%;padding-right:0%;">
          <li class="text-right" style="background-color:pink;margin-right:20px;"><div id="circle">&nbsp;</div></li>
          <li class="text-right" style="background-color:red;margin-right:20px;margin-top:20px;" id="uname"><i id="clrhello">Hello</i>, <b id="unamecolor">{{ session('loggedinusername') }}</b></li>
          <li class="text-right" style="background-color:blue;" id="logot"><a href="#" id="linkini"><b>LOGOUT</b></a></li>
		  
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="#">Action</a></li>
              <li><a href="#">Another action</a></li>
              <li><a href="#">Something else here</a></li>
              <li class="divider"></li>
              <li class="dropdown-header">Nav header</li>
              <li><a href="#">Separated link</a></li>
              <li><a href="#">One more separated link</a></li>
            </ul>
          </li>
        </ul>
		  -->
	  <!--
      </div>
	  /.nav-collapse -->
	<!--
    </div>
	/.container-fluid 
  </nav>			
	-->
			
			<div class="col-md-12" id="garisijo" style="">
			</div>
			<div class="col-md-12" id="announce" style="">
			</div>			
		</div>
	</div>	

    @yield('content')
    
    <!-- Prerequisites JavaScripts -->
    <script src="{{ URL::asset('public/js/jquery/jquery-3.1.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('public/js/bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('public/js/angularjs/angular.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('public/js/angularjs/loading-bar.min.js') }}" type="text/javascript"></script>
    
    @yield('scripts')
</body>
</html>