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
/**********************************
Responsive navbar-brand image CSS
- Remove navbar-brand padding for firefox bug workaround
- add 100% height and width auto ... similar to how bootstrap img-responsive class works
***********************************/

.navbar-brand {
  padding: 0px;
}
.navbar-brand>img {
  height: 100%;
  padding: 15px;
  width: auto;
}







/*************************
EXAMPLES 2-7 BELOW 
**************************/

/* EXAMPLE 2 (larger logo) - simply adjust top bottom padding to make logo larger */

.example2 .navbar-brand>img {
  padding: 7px 15px;
}


/* EXAMPLE 3

line height is 20px by default so add 30px top and bottom to equal the new .navbar-brand 80px height  */

.example3 .navbar-brand {
  height: 80px;
}

.example3 .nav >li >a {
  padding-top: 30px;
  padding-bottom: 30px;
}
.example3 .navbar-toggle {
  padding: 10px;
  margin: 25px 15px 25px 0;
}


/* EXAMPLE 4 - Small Narrow Logo*/
.example4 .navbar-brand>img {
  padding: 7px 14px;
}


/* EXAMPLE 5 - Logo with Text*/
.example5 .navbar-brand {
  display: flex;
  align-items: center;
}
.example5 .navbar-brand>img {
  padding: 7px 14px;
}


/* EXAMPLE 6 - Background Logo*/
.example6 .navbar-brand{ 
  background: url(http://res.cloudinary.com/candidbusiness/image/upload/v1455406304/dispute-bills-chicago.png) center / contain no-repeat;
  width: 200px;
}





/* EXAMPLE 8 - Center on mobile*/
	@media only screen and (max-width : 768px){
  .example-8 .navbar-brand {
  padding: 0px;
  transform: translateX(-50%);
  left: 50%;
  position: absolute;
}
.example-8 .navbar-brand>img {
  height: 100%;
  width: auto;
  padding: 7px 14px; 
}
}


/* EXAMPLE 8 - Center Background */
.example-8 .navbar-brand {
  background: url(http://res.cloudinary.com/candidbusiness/image/upload/v1455406304/dispute-bills-chicago.png) center / contain no-repeat;
  width: 200px;
  transform: translateX(-50%);
  left: 50%;
  position: absolute;
}





/* EXAMPLE 9 - Center with Flexbox and Text*/
.brand-centered {
  display: flex;
  justify-content: center;
  position: absolute;
  width: 100%;
  left: 0;
  top: 0;
}
.brand-centered .navbar-brand {
  display: flex;
  align-items: center;
}





/* CSS Transform Align Navbar Brand Text ... This could also be achieved with table / table-cells */
.navbar-alignit .navbar-header {
	  -webkit-transform-style: preserve-3d;
  -moz-transform-style: preserve-3d;
  transform-style: preserve-3d;
  height: 50px;
}
.navbar-alignit .navbar-brand {
	top: 50%;
	display: block;
	position: relative;
	height: auto;
	transform: translate(0,-50%);
	margin-right: 15px;
  margin-left: 15px;
}





.navbar-nav>li>.dropdown-menu {
	z-index: 9999;
}

body {
  font-family: "Lato";
}
</style>	
	
</head>
<body>

	<div class="container">
		<div class="row">
			<!--
			<div class="col-md-12" style="" id="headerdashboard">
				<div class="col-md-3" id="logos">
					<img src="public/img/bpjstk_logo600x140.png" class="img-responsive">
				</div>
				
				<div class="col-md-1 col-md-offset-4">
					<div id="circle">&nbsp;</div>
				</div>
				
				<div class="col-md-2 text-right" id="uname">
					<div>
						<i id="clrhello">Hello</i>, <b id="unamecolor">{{ session('loggedinusername') }}</b>
					</div>
				</div>
				
				<div class="col-md-2 text-right" id="logot" >
					<a href="#" id="linkini"><b>LOGOUT</b></a>
				</div>
			</div>
			-->
			
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="http://disputebills.com">
			<!--
			<img src="http://res.cloudinary.com/candidbusiness/image/upload/v1455406304/dispute-bills-chicago.png" alt="Dispute Bills">
			-->
			<img src="public/img/bpjstk_logo600x140.png" class="img-responsive">
        </a>
      </div>
      <div id="navbar1" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class="active"><a href="#">Home</a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact</a></li>
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
      </div>
      <!--/.nav-collapse -->
    </div>
    <!--/.container-fluid -->
  </nav>			
			
			<div class="col-md-12" id="garisijo">
			</div>
			<div class="col-md-12" id="announce">
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