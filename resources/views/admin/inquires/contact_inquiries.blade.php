@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Contact Inquiries</h3>
        <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Home</li>
                    <li class="breadcrumb-item active">Inquires</li>
                    <li class="breadcrumb-item active">Contact Inquiries</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right">
            <button id="bulkDelete" class="btn btn-danger mr-1 mb-1">Delete Selected</button>
        </div>
    </div>
</div>
<section id="configuration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Contact Inquiries Info</h4>
                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered yajra-datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Name</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Message</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
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
    if ($('.yajra-datatable').length) {
        CRUDManager.init({
            tableSelector: '.yajra-datatable',
            entity: 'contact',
            routes: {
                data: "{{ route('admin.contact.data') }}",
                delete: "{{ route('admin.contact.destroy', ':id') }}",
                bulkDelete: "{{ route('admin.contact.bulkDelete') }}"
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
                { data: 'form_name', name: 'form_name' },
                { data: 'fname', name: 'fname' },
                { data: 'email', name: 'email' },
                { data: 'notes', name: 'notes', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    }
});

</script>
@endpush
