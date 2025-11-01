@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Banner Management</h3>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <button id="bulkDelete" class="btn btn-danger mr-1 mb-1">Delete Selected</button>
            <a class="btn btn-info mb-1" href="{{ url('admin/banner/create') }}">Add Banner</a>
            <a class="btn btn-warning ml-1 mb-1" href="{{ route('admin.banner.trash') }}">View Trashed Banners</a>
        </div>
    </div>
</div>

<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Banner List</h4>
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
                                    <th class="select-all-col"><input type="checkbox" id="selectAll"></th>
                                    <th>S.No</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="text-center">Sort</th>
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
        entity: 'banner',
        routes: {
            data: "{{ route('admin.banner.data') }}",
            delete: "{{ route('admin.banner.destroy', ':id') }}",
            toggleStatus: "{{ route('admin.banner.toggleStatus', ':id') }}",
            bulkDelete: "{{ route('admin.banner.bulkDelete') }}",
            sort: "{{ route('admin.banner.sort') }}"
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
            {data: 'title', name: 'title'},
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'reorder-handle text-center',
                render: () => `<span class="drag-handle" style="cursor:grab;font-size:18px;">&#9776;</span>`
            },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        extraFilters: function() {
            return {
                status: $('#statusFilter').val(),
                role: $('#roleFilter').val(),
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val(),
            };
        }
    });

    $(document).on('mouseenter', '.toggleBannerStatus', function() {
        $(this).attr('title', $(this).is(':checked') ? 'Active' : 'Inactive');
    });

    // 🔽 Apply filter dynamically
    $('#statusFilter, #fromDate, #toDate').on('change', function () {
        $('.yajra-datatable').DataTable().ajax.reload();
    });
    $('#resetFilters').on('click', function () {
        $('#statusFilter').val('');
        $('#fromDate').val('');
        $('#toDate').val('');
        $('.yajra-datatable').DataTable().ajax.reload();
    });
});
</script>
@endpush
