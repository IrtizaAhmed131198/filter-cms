@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Trashed Attributes</h3>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <button id="bulkRestore" class="btn btn-success mb-1">Restore Selected</button>
            <button id="bulkForceDelete" class="btn btn-danger mb-1 ml-1 mr-1">Delete Permanently</button>
            <a class="btn btn-info mb-1" href="{{ route('admin.attribute.index') }}">Back to Active Attributes</a>
        </div>
    </div>
</div>

<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Deleted Attributes</h4>
                </div>
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th class="select-all-col"><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Values</th>
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
    entity: 'attribute',
    routes: {
        data: "{{ route('admin.attribute.trash.data') }}",
        restore: "{{ route('admin.attribute.restore', ':id') }}",
        forceDelete: "{{ route('admin.attribute.forceDelete', ':id') }}",
        bulkRestore: "{{ route('admin.attribute.bulkRestore') }}",
        bulkForceDelete: "{{ route('admin.attribute.bulkForceDelete') }}"
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
        {data: 'value', name: 'value', orderable: false, searchable: false},
        {data: 'deleted_at', name: 'deleted_at'},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
});
</script>
@endpush
