@extends('layouts.app')

@section('content')
<div class="container-fluid bg-white mt-5">
    <div class="row">
        <div class="col-sm-12">
            <div class="card white-box">
                <div class="card-body">
                    <h3 class="box-title">News Details</h3>
                    <a class="btn btn-success pull-right" href="{{ route('admin.news.index') }}">
                        <i class="icon-arrow-left-circle"></i> Back</a>
                    <hr>
                    <table class="table table-bordered">
                        <tr><th>ID</th><td>{{ $news->id }}</td></tr>
                        <tr><th>Title</th><td>{{ $news->title }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.admin.footer')
</div>
@endsection
