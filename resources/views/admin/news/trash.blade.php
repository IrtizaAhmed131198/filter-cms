@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Trashed Newss</h3>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <a class="btn btn-info mb-1" href="{{ route('admin.news.index') }}">Back to Active Newss</a>
        </div>
    </div>
</div>
<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Deleted Newss</h4></div>
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Deleted At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
CRUDManager.init({
    tableSelector: '.yajra-datatable',
    entity: 'news',
    routes: {
        data: "{{ route('admin.news.trash.data') }}",
        restore: "{{ route('admin.news.restore', ':id') }}",
        forceDelete: "{{ route('admin.news.forceDelete', ':id') }}"
    },
    columns: [
        {data: 'id', name: 'id'},
        {data: 'title', name: 'title'},
        {data: 'image', name: 'image', orderable: false, searchable: false},
        {data: 'deleted_at', name: 'deleted_at'},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
});
</script>
@endpush
