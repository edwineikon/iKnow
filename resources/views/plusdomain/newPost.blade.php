@extends('shared.layout')
@section('title', 'Create a New Post')
@section('content')
<div class="container">
    <ol class="breadcrumb">
        <li><a href="{{ url('+/home') }}">Timeline</a></li>
        <li class="active">Create a New Post</li>
    </ol>
    <h2>Create a New Post</h2>
    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('+/newpost') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="txtTitle">Title</label>
                    <input type="text" class="form-control" name="txtTitle" id="txtTitle" placeholder="Title">
                </div>
                <div class="form-group">
                    <label for="txtBody">Content</label>
                    <textarea class="form-control" name="txtBody" id="txtBody" rows="3" placeholder="Content"></textarea>
                </div>
                <div class="form-group">
                    <label for="fileInputMedia">File Media</label>
                    <input accept="video/*,image/*" type="file" name="media" id="fileInputMedia">
                    <p class="help-block">Choose image / video.</p>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <!-- TODO: add angular mvc script -->
@endsection