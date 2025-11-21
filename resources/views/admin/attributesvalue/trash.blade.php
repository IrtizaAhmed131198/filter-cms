@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Trashed Attribute Values</h3>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <button id="bulkRestore" class="btn btn-success mb-1">Restore Selected</button>
            <button id="bulkForceDelete" class="btn btn-danger mb-1 ml-1 mr-1">Delete Permanently</button>
            <a class="btn btn-info mb-1" href="{{ route('admin.attributesvalue.index') }}">Back to Active Attribute Values</a>
        </div>
    </div>
</div>

<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Deleted Attribute Values</h4>
                </div>
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th class="select-all-col"><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Attribute</th>
                                    <th>Value</th>
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
    entity: 'attributesvalue',
    routes: {
        data: "{{ route('admin.attributesvalue.trash.data') }}",
        restore: "{{ route('admin.attributesvalue.restore', ':id') }}",
        forceDelete: "{{ route('admin.attributesvalue.forceDelete', ':id') }}",
        bulkRestore: "{{ route('admin.attributesvalue.bulkRestore') }}",
        bulkForceDelete: "{{ route('admin.attributesvalue.bulkForceDelete') }}"
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
        {data: 'attribute', name: 'attribute'},
        {data: 'value', name: 'value'},
        {data: 'deleted_at', name: 'deleted_at'},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
});
</script>
@endpush
