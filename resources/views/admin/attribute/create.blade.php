@extends('layouts.app')

@push('before-css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.bootstrap-tagsinput {
    width: 100%;
    min-height: 40px;
    display: block;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
}

.bootstrap-tagsinput .tag {
    margin-right: 2px;
    color: white;
    background-color: #5A8DEE;
    padding: 0.2em 0.5em;
    border-radius: 0.25rem;
}
</style>
@endpush

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">Create New Attribute</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.attribute.index') }}">Attribute Management</a>
                        </li>
                        <li class="breadcrumb-item active">Create New Attribute</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="basic-form-layouts">
            <div class="row match-height">
                <!-- Left Side: Form -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Attribute Info</h4>
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
                                <form class="form" enctype="multipart/form-data" method="POST"
                                    action="{{ route('admin.attribute.store') }}">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- Attribute Name -->
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" class="form-control" required name="name"
                                                        id="name" placeholder="Enter Attribute Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions text-right pb-0">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="la la-check-square-o"></i> Add
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Info / Validation -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Information</h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        </div>

                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="card-text">
                                    @if ($errors->any())
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="alert alert-danger">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if (Session::has('message'))
                                        <ul>
                                            <li class="alert alert-success">{{ Session::get('message') }}</li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('.select2-tags').select2({
        tags: true,
        tokenSeparators: [','],
        width: '100%'
    });
</script>
@endpush
