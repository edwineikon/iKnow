@extends('shared.layoutwithoutnavbar')
@section('title', 'Login')
@section('content')
<div class="container">
	<!--
    <h2>Please Sign In</h2>
	-->
    <div class="row">
		<div class="col-md-3" style="">
		</div>
		<!-- LOGIN -->
        <div id="coba" class="col-md-6" style="">
            <!-- TODO: Form Login -->
				<div class="col-md-2">
				</div>
				<div class="col-md-8">
					<div class="">
						<img src="public/img/bpjs_logo600x140.png" class="img-responsive" alt="Cinque Terre">
					</div>
					</br></br>
					<div class="input-group">				
					  <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
					  <input type="text" class="form-control input-lg" placeholder="Username" aria-describedby="basic-addon1">
					</div>
					</br>
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
					  <input type="password" class="form-control input-lg" placeholder="Password" aria-describedby="basic-addon1">
					</div>
					</br>
					<div class="text-right">
						<a href="" class="">Forgot Password</a>
					</div>
					</br>

					<div class="text-center">
						<button type="button" class="btn btn-success btn-block btn-lg">LOGIN</button>
					</div>
				</div>
				<div class="col-md-2">
				</div>				
        </div>
		<!-- -->
		<div class="col-md-3" style="">
		</div>
    </div>
</div>
@endsection