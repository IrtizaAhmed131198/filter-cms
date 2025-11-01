@extends('layouts.app')
@push('before-css')
<link rel="stylesheet" href="{{ asset('plugins/vendors/dropify/dist/css/dropify.min.css') }}">
<style>
    .user-card {
        height: unset !important;
    }
</style>
@endpush
@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
        <h3 class="content-header-title mb-0 d-inline-block">Edit User</h3>
        <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('admin/users') }}">User Management</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section id="basic-form-layouts">
        <div class="row match-height">
            <div class="col-md-7">
                <div class="card user-card">
                    <div class="card-header">
                        <h4 class="card-title">Edit User Info</h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body">

                            <form method="POST" action="{{ route('admin.users.update', $user->id) }}"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Tabs Navigation --}}
                                <ul class="nav nav-tabs nav-underline" id="userTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#user-info" role="tab">User Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#bio" role="tab">Bio Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#address-info" role="tab">Address Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#role-info" role="tab">User Role Info</a>
                                    </li>
                                </ul>

                                {{-- Tabs Content --}}
                                <div class="tab-content pt-2" id="userTabContent">

                                    {{-- User Info --}}
                                    <div class="tab-pane fade show active" id="user-info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control"
                                                       value="{{ old('name', $user->name) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control"
                                                       value="{{ old('email', $user->email) }}" required>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Password (Leave blank if unchanged)</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Bio Info --}}
                                    <div class="tab-pane fade" id="bio" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Date of Birth</label>
                                                <input type="date" name="dob" class="form-control"
                                                       value="{{ old('dob', $user->profile->dob ?? '') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label>About / Bio</label>
                                                <textarea name="bio" rows="4" class="form-control">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label>Profile Picture</label>
                                                <input type="file" name="pic" class="dropify"
                                                    data-default-file="{{ asset($user->profile?->pic ?? 'assets/imgs/noimage.png') }}"
                                                    data-height="120" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Address Info --}}
                                    <div class="tab-pane fade" id="address-info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Gender</label>
                                                <select class="form-control" name="gender">
                                                    <option value="">Select</option>
                                                    <option value="male" {{ old('gender', $user->profile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                    <option value="female" {{ old('gender', $user->profile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                                    <option value="other" {{ old('gender', $user->profile->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Country</label>
                                                <input type="text" name="country" class="form-control"
                                                       value="{{ old('country', $user->profile->country ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>State</label>
                                                <input type="text" name="state" class="form-control"
                                                       value="{{ old('state', $user->profile->state ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>City</label>
                                                <input type="text" name="city" class="form-control"
                                                       value="{{ old('city', $user->profile->city ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Address</label>
                                                <input type="text" name="address" class="form-control"
                                                       value="{{ old('address', $user->profile->address ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Postal/Zip</label>
                                                <input type="text" name="postal" class="form-control"
                                                       value="{{ old('postal', $user->profile->postal ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Role Info --}}
                                    <div class="tab-pane fade" id="role-info" role="tabpanel">
                                        <p class="text-danger"><strong>Be careful with role selection!</strong></p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>User Role</label>
                                                <select class="form-control" name="role" id="role" required>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}"
                                                            {{ old('role', $user->role ?? '') == $role->id ? 'selected' : '' }}>
                                                            {{ $role->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-check"></i> Update User
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Errors / Info --}}
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Information</h4>
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
                                    <ul><li class="alert alert-success">{{ Session::get('message') }}</li></ul>
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
<script src="{{ asset('plugins/vendors/dropify/dist/js/dropify.min.js') }}"></script>
<script>
    $(function() {
        $('.dropify').dropify();
    });
</script>
@endpush
