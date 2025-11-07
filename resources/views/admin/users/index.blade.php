@extends('layouts.app')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">User Management</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item">User</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                @canAccess('delete_users')
                    <button id="bulkDelete" class="btn btn-danger mr-1 mb-1">Delete Selected</button>
                @endcanAccess

                @canAccess('create_users')
                    <a class="btn btn-info mb-1" href="{{ url('admin/users/create') }}">Add User</a>
                @endcanAccess

                @canAccess('view_trash_users')
                    <a class="btn btn-warning ml-1 mb-1" href="{{ route('admin.users.trash') }}">View Trashed Users</a>
                @endcanAccess
            </div>
        </div>
    </div>

    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">User List</h4>
                    </div>
                    <div class="card-body card-dashboard">
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="custom-stat-card total">
                                    <div class="icon">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <div class="details">
                                        <h6>Users</h6>
                                        <h3 id="totalUsers">{{ $total_users ?? 0 }}</h3>
                                        <p>Total Users</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="custom-stat-card verified">
                                    <div class="icon">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <div class="details">
                                        <h6>Verified Users</h6>
                                        <h3 id="verifiedUsers">{{ $active_users ?? 0 }}</h3>
                                        <p>Active & Approved</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="custom-stat-card pending">
                                    <div class="icon">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <div class="details">
                                        <h6>Verification Pending</h6>
                                        <h3 id="pendingUsers">{{ $inactive_users ?? 0 }}</h3>
                                        <p>Awaiting Approval</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="custom-stat-card duplicate">
                                    <div class="icon">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <div class="details">
                                        <h6>Duplicate Users</h6>
                                        <h3 id="duplicateUsers">0</h3>
                                        <p>Duplicate Emails</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-end">
                            <div class="col-md-2">
                                <label>Role</label>
                                <select id="roleFilter" class="form-control">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->label }}</option>
                                    @endforeach
                                </select>
                            </div>

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
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Profile Pic</th>
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
                entity: 'users',
                routes: {
                    data: "{{ route('admin.users.data') }}",
                    delete: "{{ route('admin.users.destroy', ':id') }}",
                    toggleStatus: "{{ route('admin.users.toggleStatus', ':id') }}",
                    bulkDelete: "{{ route('admin.users.bulkDelete') }}"
                },
                columns: [{
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
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

                extraFilters: function() {
                    return {
                        status: $('#statusFilter').val(),
                        role: $('#roleFilter').val(),
                        from_date: $('#fromDate').val(),
                        to_date: $('#toDate').val(),
                    };
                },
            });

            $(document).on('mouseenter', '.toggleUsersStatus', function() {
                $(this).attr('title', $(this).is(':checked') ? 'Active' : 'Inactive');
            });

            // 🔽 Apply filter dynamically
            $('#statusFilter, #roleFilter, #fromDate, #toDate').on('change', function () {
                $('.yajra-datatable').DataTable().ajax.reload();
            });
            $('#resetFilters').on('click', function () {
                $('#statusFilter').val('');
                $('#roleFilter').val('');
                $('#fromDate').val('');
                $('#toDate').val('');
                $('.yajra-datatable').DataTable().ajax.reload();
            });

            function lazyLoadImages() {
                const lazyImages = document.querySelectorAll('.lazy-image[data-src]');
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            obs.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => observer.observe(img));
            }

            $('.yajra-datatable').on('draw.dt', function () {
                lazyLoadImages();
            });
        });
    </script>
@endpush
