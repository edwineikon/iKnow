@extends('shared.layoutwithoutnavbar')
@section('title', 'Login')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-4">
		</div>
		<!-- LOGIN -->
		<div id="coba" class="col-md-4">
			<form action="{{ url('login') }}" method="post">
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
			</form>				
		</div>
		<div class="col-md-4">
		</div>
	</div>
</div>
@endsection