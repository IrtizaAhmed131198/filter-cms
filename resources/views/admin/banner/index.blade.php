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
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th class="select-all-col"><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Status</th>
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
            bulkDelete: "{{ route('admin.banner.bulkDelete') }}"
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
            {data: 'title', name: 'title'},
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
});
</script>
@endpush
