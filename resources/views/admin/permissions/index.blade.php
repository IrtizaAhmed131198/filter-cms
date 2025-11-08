@extends('layouts.app')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">Permission Management</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" href="{{url('admin/dashboard')}}">Home</li>
                        <li class="breadcrumb-item active">Add & Assign Permissions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="basic-form-layouts">
            <div class="row match-height">
                <div class="col-md-7">
                    <!-- Add New Permission -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Permission</h4>
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
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.permissions.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="text" name="name" class="form-control" placeholder="Permission Name (e.g., view_users)" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="label" class="form-control" placeholder="Label (optional)">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">
                                            <i class="la la-check-square-o"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Permissions to Roles -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Assign Permissions to Roles</h4>
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
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.permissions.assign') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="role">Select Role:</label>
                                    <select name="role_id" id="role" class="form-control" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ ucfirst($role->label) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Permissions:</label>

                                    @foreach($permissions as $module => $modulePermissions)
                                        <div class="card mt-2 shadow-sm border">
                                            <div class="card-header bg-light py-2">
                                                <strong>{{ $module }}</strong>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($modulePermissions->sortBy('name') as $permission)
                                                        <div class="col-md-3">
                                                            <div class="form-check">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    name="permissions[]"
                                                                    value="{{ $permission->id }}"
                                                                    id="perm{{ $permission->id }}"
                                                                >
                                                                <label class="form-check-label" for="perm{{ $permission->id }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="submit" class="btn btn-success">Save Permissions</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Information</h4>
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
                            <div class="card-body">
                                <div class="card-text">
                                    @if ($errors->any())
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li class="alert alert-danger">
                                            {{ $error }}
                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                    @if(Session::has('message'))
                                    <ul>
                                        <li class="alert alert-success">
                                            {{ Session::get('message') }}
                                        </li>
                                    </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Permissions List</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered yajra-datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Label</th>
                                        <th>Created At</th>
                                        <th width="80px">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    // Function to uncheck all checkboxes
    function resetPermissions() {
        permissionCheckboxes.forEach(cb => cb.checked = false);
    }

    roleSelect.addEventListener('change', function() {
        const roleId = this.value;

        if (!roleId) return;

        let url = "{{ route('admin.permissions.role.permissions', ':id') }}";
        url = url.replace(':id', roleId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                resetPermissions();

                if (data.permissions && Array.isArray(data.permissions)) {
                    data.permissions.forEach(id => {
                        const checkbox = document.querySelector(`#perm${id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            })
            .catch(err => console.error('Error fetching role permissions:', err));
    });

    // Trigger change on load to preselect for first role
    roleSelect.dispatchEvent(new Event('change'));
});

$(function () {
    let table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.permissions.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'label', name: 'label'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    // Handle delete
    $(document).on('click', '.delete-permission', function() {
        let id = $(this).data('id');
        let url = "{{ route('admin.permissions.delete', ':id') }}";
        url = url.replace(':id', id);
        if (confirm("Are you sure you want to delete this permission?")) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(res) {
                    if (res.success) {
                        $.toast({
                            heading: 'Success',
                            text: res.message,
                            showHideTransition: 'slide',
                            icon: 'success',
                            position: 'top-right'
                        });
                        table.ajax.reload();
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: res.message || 'Something went wrong',
                            showHideTransition: 'fade',
                            icon: 'error',
                            position: 'top-right'
                        });
                    }
                },
                error: function() {
                    $.toast({
                        heading: 'Error',
                        text: 'Server error occurred.',
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-right'
                    });
                }
            });
        }
    });
});
</script>
@endpush
