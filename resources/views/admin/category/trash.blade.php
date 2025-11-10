@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Trashed Categories</h3>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <button id="bulkRestore" class="btn btn-success mb-1">Restore Selected</button>
            <button id="bulkForceDelete" class="btn btn-danger mb-1 ml-1 mr-1">Delete Permanently</button>
            <a class="btn btn-info mb-1" href="{{ route('admin.category.index') }}">Back to Active Categories</a>
        </div>
    </div>
</div>

<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Deleted Categories</h4>
                </div>
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th class="select-all-col"><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
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
    entity: 'category',
    routes: {
        data: "{{ route('admin.category.trash.data') }}",
        restore: "{{ route('admin.category.restore', ':id') }}",
        forceDelete: "{{ route('admin.category.forceDelete', ':id') }}",
        bulkRestore: "{{ route('admin.category.bulkRestore') }}",
        bulkForceDelete: "{{ route('admin.category.bulkForceDelete') }}"
    },
    columns: [
        {
            data: 'id',
            name: 'checkbox',
            orderable: false,
            searchable: false,
            render: function(data) {
                return `<input type="checkbox" class="rowCheckbox" value="${data}">`;
            }
        },
        {data: 'id', name: 'id'},
        {data: 'name', name: 'name'},
        {data: 'description', name: 'description'},
        {data: 'deleted_at', name: 'deleted_at'},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
});
</script>
@endpush
