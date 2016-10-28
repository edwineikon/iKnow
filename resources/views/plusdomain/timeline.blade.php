@extends('shared.layout')
@section('title', 'Timeline')
@section('content')
<div class="container">
    <h2>Timeline</h2>
    <div class="row">
        <div class="col-md-12">
            <p>
                <a href="{{ url('+/newpost') }}" class="btn btn-primary active" role="button">Create a New Post</a>
            </p>
            <p>
                TODO: Show something
            </p>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <!-- TODO: add angular mvc script -->
@endsection