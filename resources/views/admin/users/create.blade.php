@extends('layouts.app')
@push('before-css')
  <link rel="stylesheet" href="{{asset('plugins/vendors/dropify/dist/css/dropify.min.css')}}">
  <style>
    .user-card {
        height: unset !important;
    }
  </style>
@endpush
@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">Create New User</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" href="{{ url('admin/dashboard') }}">Home</li>
                        <li class="breadcrumb-item"><a href="{{ url('admin/users') }}">User Management</a></li>
                        <li class="breadcrumb-item active">Create New User</li>
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
                            <h4 class="card-title" id="basic-layout-form">User Info</h4>
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

                                {{-- ✅ Start of ONE form --}}
                                <form method="POST" action="{{ route('admin.users.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    {{-- Tabs Navigation --}}
                                    <ul class="nav nav-tabs nav-underline" id="userTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="user-info-tab" data-toggle="tab"
                                                href="#user-info" role="tab">User Info</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="bio-tab" data-toggle="tab" href="#bio"
                                                role="tab">Bio Info</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="address-info-tab" data-toggle="tab" href="#address-info"
                                                role="tab">Address Info</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="role-info-tab" data-toggle="tab" href="#role-info"
                                                role="tab">User Role Info</a>
                                        </li>
                                    </ul>

                                    {{-- Tabs Content --}}
                                    <div class="tab-content pt-2" id="userTabContent">

                                        {{-- Tab 1: User Info --}}
                                        <div class="tab-pane fade show active" id="user-info" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ old('name') }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ old('email') }}" required>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>Password</label>
                                                    <input type="password" name="password" class="form-control" required>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control"
                                                        required>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Tab 2: Bio Info --}}
                                        <div class="tab-pane fade" id="bio" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Date of Birth</label>
                                                    <input value="{{old('dob')}}" autocomplete="off" id="dob" name="dob"
                                                        type="date" class="form-control"
                                                        data-date-format="YYYY-MM-DD"
                                                        placeholder="yyyy-mm-dd"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>About / Bio</label>
                                                    <textarea name="bio" rows="4" class="form-control" placeholder="Write short bio...">{{ old('bio') }}</textarea>
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <label>Profile Picture</label>
                                                    <input type="file" name="pic" class="dropify"
                                                        data-height="120" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Tab 3: Address Info --}}
                                        <div class="tab-pane fade" id="address-info" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Gender</label>
                                                    <select class="form-control" title="Select Gender..." name="gender">
                                                        <option value="">Select</option>
                                                        <option value="male"
                                                                @if(old('gender') === 'male') selected="selected" @endif >
                                                            Male
                                                        </option>
                                                        <option value="female"
                                                                @if(old('gender') === 'female') selected="selected" @endif >
                                                            Female
                                                        </option>
                                                        <option value="other"
                                                                @if(old('gender') === 'other') selected="selected" @endif >
                                                            Other
                                                        </option>

                                                    </select>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>Country</label>
                                                    <input id="countries" name="country" type="text"
                                                        class="form-control"
                                                        value="{!! old('country') !!}"/>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>State</label>
                                                    <input id="state" name="state" type="text"
                                                        class="form-control"
                                                        value="{!! old('state') !!}"/>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>City</label>
                                                    <input id="city" name="city" type="text" class="form-control"
                                                        value="{!! old('city') !!}"/>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>Address</label>
                                                    <input id="address" name="address" type="text" class="form-control"
                                                        value="{{ old('address') }}"/>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label>Postal/Zip</label>
                                                    <input id="postal" name="postal" type="text" class="form-control"
                                                        value="{!! old('postal') !!}"/>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Tab 4: User Role Info --}}
                                        <div class="tab-pane fade" id="role-info" role="tabpanel">
                                            <p class="text-danger"><strong>Be careful with role selection, if you give admin
                                                access.. they can access admin section</strong></p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>User Role</label>
                                                    <select class="form-control" title="Select role..." name="role" id="role" required>
                                                        <option value="" disabled selected>Select Role</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                    @if($role->id == old('role')) selected="selected" @endif >{{ $role->label}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="la la-check"></i> Add User
                                        </button>
                                    </div>

                                </form>
                                {{-- ✅ End of ONE form --}}

                            </div>
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
                                    @if (Session::has('message'))
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
                </div>
            </div>
        </section>
    </div>
@endsection
@push('js')
  <script src="{{asset('plugins/vendors/dropify/dist/js/dropify.min.js')}}"></script>
  <script>
      $(function() {
          $('.dropify').dropify();
      });
  </script>
@endpush
