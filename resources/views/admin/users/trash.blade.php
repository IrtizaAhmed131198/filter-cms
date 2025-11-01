@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Trashed Users</h3>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <button id="bulkRestore" class="btn btn-success mb-1">Restore Selected</button>
            <button id="bulkForceDelete" class="btn btn-danger mb-1 ml-1 mr-1">Delete Permanently</button>
            <a class="btn btn-info mb-1" href="{{ route('admin.users.index') }}">Back to Active Users</a>
        </div>
    </div>
</div>

<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Deleted Users</h4>
                </div>
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th class="select-all-col"><input type="checkbox" id="selectAll"></th>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
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
    entity: 'banner',
    routes: {
        data: "{{ route('admin.users.trash.data') }}",
        restore: "{{ route('admin.users.restore', ':id') }}",
        forceDelete: "{{ route('admin.users.forceDelete', ':id') }}",
        bulkRestore: "{{ route('admin.users.bulkRestore') }}",
        bulkForceDelete: "{{ route('admin.users.bulkForceDelete') }}"
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
        {
            data: null,
            name: 'id',
            orderable: true,
            searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {data: 'name', name: 'name'},
        {data: 'email', name: 'email'},
        {data: 'role', name: 'role', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
});
</script>
@endpush
