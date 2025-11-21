@extends('layouts.app')

@push('before-css')
@endpush

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Edit Attribute Value</h3>
        <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.attributesvalue.index') }}">Attribute Value Management</a></li>
                    <li class="breadcrumb-item active">Edit Attribute Value</li>
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
                        <h4 class="card-title">Attribute Value Info</h4>
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
                                action="{{ route('admin.attributesvalue.update', $attributeValue->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <!-- Attribute Name -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="attribute_id">Attribute</label>
                                                <select class="form-control" required name="attribute_id" id="attribute_id">
                                                    <option value="">Select Attribute</option>
                                                    @foreach($attributes as $attribute)
                                                        <option value="{{ $attribute->id }}" {{ old('attribute_id', $attributeValue->attribute_id) == $attribute->id ? 'selected' : '' }}>
                                                            {{ $attribute->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Attribute Value -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="value">Value</label>
                                                <input type="text" class="form-control" required name="value"
                                                    id="value" placeholder="Enter Attribute Value" value="{{ old('value', $attributeValue->value) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions text-right pb-0">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-check-square-o"></i> Update
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
@endpush
