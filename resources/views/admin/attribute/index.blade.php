@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Attribute Management</h3>
        <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Home</li>
                    <li class="breadcrumb-item">Attributes</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            @canAccess('delete_attribute')
                <button id="bulkDelete" class="btn btn-danger mr-1 mb-1">Delete Selected</button>
            @endcanAccess

            @canAccess('create_attribute')
                <a class="btn btn-info mb-1" href="{{ route('admin.attribute.create') }}">Add Attribute</a>
            @endcanAccess

            @canAccess('view_trash_attribute')
                <a class="btn btn-warning ml-1 mb-1" href="{{ route('admin.attribute.trash') }}">View Trashed Attributes</a>
            @endcanAccess
        </div>
    </div>
</div>

<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Attribute List</h4>
                </div>
                <div class="card-body card-dashboard">
                    <div class="row mb-4 align-items-end">
                        <div class="col-md-2">
                            <label>Status</label>
                            <select id="statusFilter" class="form-control">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Date From</label>
                            <input type="date" id="fromDate" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label>Date To</label>
                            <input type="date" id="toDate" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <button id="resetFilters" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Created At</th>
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
$(function() {
    CRUDManager.init({
        tableSelector: '.yajra-datatable',
        entity: 'attribute',
        routes: {
            data: "{{ route('admin.attribute.data') }}",
            delete: "{{ route('admin.attribute.destroy', ':id') }}",
            toggleStatus: "{{ route('admin.attribute.toggleStatus', ':id') }}",
            bulkDelete: "{{ route('admin.attribute.bulkDelete') }}"
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: data => `<input type="checkbox" class="rowCheckbox" value="${data}">`
            },
            {
                data: null,
                name: 'id',
                render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'name', name: 'name' },
            {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false
            },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        extraFilters: function() {
            return {
                status: $('#statusFilter').val(),
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val(),
            };
        }
    });

    // 🔁 Refresh on filter change
    $('#statusFilter, #fromDate, #toDate').on('change', function () {
        $('.yajra-datatable').DataTable().ajax.reload();
    });

    // 🔄 Reset filters
    $('#resetFilters').on('click', function () {
        $('#statusFilter').val('');
        $('#fromDate').val('');
        $('#toDate').val('');
        $('.yajra-datatable').DataTable().ajax.reload();
    });
});
</script>
@endpush
