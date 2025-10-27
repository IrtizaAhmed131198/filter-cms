<?php
// php artisan make:crud-views Banner
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrudViews extends Command
{
    protected $signature = 'make:crud-views {name}';
    protected $description = 'Generate dynamic CRUD Blade views for a given crud (e.g., banner, product, etc.)';

    public function handle()
    {
        $rawName = trim($this->argument('name'));

        // ❌ Validation: disallow spaces or underscores
        if (preg_match('/[\s_]/', $rawName)) {
            $this->error("❌ Invalid name format: '{$rawName}'.
                Use a single word or PascalCase name only (e.g., Book, Product, Banner).");
            return Command::FAILURE;
        }

        $name = strtolower($rawName); // e.g. banner
        $studly = Str::studly($rawName); // e.g. Banner
        $path = resource_path("views/admin/{$name}");

        File::ensureDirectoryExists($path);

        $this->info("📁 Directory ready: resources/views/admin/{$name}");

        $views = [
            'index'  => $this->getIndexTemplate($name, $studly),
            'create' => $this->getCreateTemplate($name, $studly),
            'edit'   => $this->getEditTemplate($name, $studly),
            'show'   => $this->getShowTemplate($name, $studly),
            'trash'  => $this->getTrashTemplate($name, $studly),
        ];

        foreach ($views as $file => $content) {
            $filePath = "{$path}/{$file}.blade.php";

            if (File::exists($filePath)) {
                if (!$this->confirm("⚠️ {$file}.blade.php already exists. Overwrite?")) {
                    $this->warn("⏩ Skipped: {$file}.blade.php");
                    continue;
                }
            }

            File::put($filePath, $content);
            $this->info("✅ Created: {$file}.blade.php");
        }

        $this->info("🎉 CRUD views generated successfully for {$studly}!");
        return 0;
    }

    /* ======================================================
     |  TEMPLATE FUNCTIONS
     ====================================================== */

    protected function getIndexTemplate($name, $studly)
    {
        $lower = strtolower($name);

        return <<<BLADE
            @extends('layouts.app')

            @section('content')
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">{$studly} Management</h3>
                </div>
                <div class="content-header-right col-md-6 col-12">
                    <div class="btn-group float-md-right">
                        <button id="bulkDelete" class="btn btn-danger mr-1 mb-1">Delete Selected</button>
                        <a class="btn btn-info mb-1" href="{{ url('admin/{$lower}/create') }}">Add {$studly}</a>
                        <a class="btn btn-warning ml-1 mb-1" href="{{ route('admin.{$lower}.trash') }}">View Trashed {$studly}s</a>
                    </div>
                </div>
            </div>

            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{$studly} List</h4>
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
                    entity: '{$lower}',
                    routes: {
                        data: "{{ route('admin.{$lower}.data') }}",
                        delete: "{{ route('admin.{$lower}.destroy', ':id') }}",
                        toggleStatus: "{{ route('admin.{$lower}.toggleStatus', ':id') }}",
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
                        { data: 'id', name: 'id' },
                        { data: 'title', name: 'title' },
                        { data: 'image', name: 'image', orderable: false, searchable: false },
                        { data: 'status', name: 'status', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            });
            </script>
            @endpush
            BLADE;
    }


    protected function getCreateTemplate($name, $studly)
    {
        $lower = strtolower($name);

        return <<<BLADE
            @extends('layouts.app')

            @push('before-css')
            <link rel="stylesheet" href="{{ asset('plugins/vendors/dropify/dist/css/dropify.min.css') }}">
            @endpush

            @section('content')

            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Create New {$studly}</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('admin/{$lower}') }}">{$studly} Management</a></li>
                                <li class="breadcrumb-item active">Create New {$studly}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form">{$studly} Info</h4>
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
                                        <form class="form" enctype="multipart/form-data" method="post" action="{{ route('admin.{$lower}.store') }}">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="title">Title</label>
                                                            <input class="form-control" required name="title" type="text" id="title">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="summary-ckeditor">Description</label>
                                                            <textarea name="description" id="summary-ckeditor" cols="30" rows="10" class="form-control" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="summary-ckeditor">{$studly} Image</label>
                                                            <div class="upload-photo">
                                                                <input type="file" name="image" id="input-file-now" class="dropify" required />
                                                            </div>
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
                                            @if (\$errors->any())
                                                <ul>
                                                    @foreach (\$errors->all() as \$error)
                                                        <li class="alert alert-danger">{{ \$error }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            @if(Session::has('message'))
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
            <script src="{{ asset('plugins/vendors/dropify/dist/js/dropify.min.js') }}"></script>
            <script>
                $(function() {
                    $('.dropify').dropify();
                });
            </script>
            @endpush
            BLADE;
    }


    protected function getShowTemplate($name, $studly)
    {
        $lower = strtolower($name);

        return <<<BLADE
            @extends('layouts.app')

            @section('content')
                <div class="container-fluid bg-white mt-5">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="white-box card">
                                <div class="card-body">
                                    <h3 class="box-title pull-left">{$studly} {{ \${$lower}->id }}</h3>
                                    @can('view-' . str_slug('{$studly}'))
                                        <a class="btn btn-success pull-right" href="{{ route('admin.{$lower}.index') }}">
                                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> Back
                                        </a>
                                    @endcan
                                    <div class="clearfix"></div>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table">
                                            <tbody>
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{ \${$lower}->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Title</th>
                                                    <td>{{ \${$lower}->title }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('layouts.admin.footer')
                </div>
            @endsection
            BLADE;
    }

    protected function getEditTemplate($name, $studly)
    {
        $lower = strtolower($name);

        return <<<BLADE
            @extends('layouts.app')
            @push('before-css')
            <link rel="stylesheet" href="{{ asset('plugins/vendors/dropify/dist/css/dropify.min.css') }}">
            @endpush
            @section('content')

            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Edit {$studly}</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item" href="{{ url('admin/dashboard') }}">Home</li>
                                <li class="breadcrumb-item"><a href="{{ url('admin/{$lower}') }}">{$studly} Management</a></li>
                                <li class="breadcrumb-item active">Edit {$studly}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form">Edit {$studly} Info</h4>
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
                                        <form class="form" enctype="multipart/form-data" method="post" action="{{ route('admin.{$lower}.update', \${$lower}->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="title">Title</label>
                                                            <input class="form-control" required name="title" type="text" id="title" value="{{ \${$lower}->title }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="summary-ckeditor">Description</label>
                                                            <textarea name="description" id="summary-ckeditor" cols="30" rows="10" class="form-control" required>{{ \${$lower}->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="summary-ckeditor">{$studly} Image</label>
                                                            <img src="{{ asset(\${$lower}->image) }}" class="d-block" alt="" width="100%">
                                                            <br>
                                                            <div class="upload-photo">
                                                                <input type="file" name="image" id="input-file-now" class="dropify" />
                                                            </div>
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
                                            @if (\$errors->any())
                                            <ul>
                                                @foreach (\$errors->all() as \$error)
                                                <li class="alert alert-danger">{{ \$error }}</li>
                                                @endforeach
                                            </ul>
                                            @endif
                                            @if(Session::has('message'))
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

            @push('js')
            <script src="{{ asset('plugins/vendors/dropify/dist/js/dropify.min.js') }}"></script>
            <script>
                $(function() {
                    $('.dropify').dropify();
                });
            </script>
            @endpush
            @endsection
            BLADE;
    }

    protected function getTrashTemplate($name, $studly)
    {
        $lower = strtolower($name);

        return <<<BLADE
            @extends('layouts.app')

            @section('content')
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Trashed {$studly}s</h3>
                </div>
                <div class="content-header-right col-md-6 col-12">
                    <div class="btn-group float-md-right">
                        <button id="bulkRestore" class="btn btn-success mb-1">Restore Selected</button>
                        <button id="bulkForceDelete" class="btn btn-danger mb-1 ml-1 mr-1">Delete Permanently</button>
                        <a class="btn btn-info mb-1" href="{{ route('admin.{$lower}.index') }}">Back to Active {$studly}s</a>
                    </div>
                </div>
            </div>

            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Deleted {$studly}s</h4>
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
                                                <th>Deleted At</th>
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
            CRUDManager.init({
                tableSelector: '.yajra-datatable',
                entity: '{$lower}',
                routes: {
                    data: "{{ route('admin.{$lower}.trash.data') }}",
                    restore: "{{ route('admin.{$lower}.restore', ':id') }}",
                    forceDelete: "{{ route('admin.{$lower}.forceDelete', ':id') }}",
                    bulkRestore: "{{ route('admin.banner.bulkRestore') }}",
                    bulkForceDelete: "{{ route('admin.banner.bulkForceDelete') }}"
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
                    {data: 'deleted_at', name: 'deleted_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            </script>
            @endpush
            BLADE;
    }
}
