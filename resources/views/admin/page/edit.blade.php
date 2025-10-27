@extends('layouts.app')
@push('before-css')
    <link rel="stylesheet" href="{{ asset('plugins/vendors/dropify/dist/css/dropify.min.css') }}">
@endpush
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">Pages Content</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Home</li>
                        <li class="breadcrumb-item active">CMS</li>
                        <li class="breadcrumb-item active">Pages Content</li>
                        <li class="breadcrumb-item active">Edit Page</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                <a class="btn btn-info mb-1" href="{{ url('/admin/page') }}">Back</a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="basic-form-layouts">
            <div class="row match-height">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-form">Edit Page #{{ $page->id }}</h4>
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
                                    action="{{ route('admin.pages.update', $page->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-body">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="page_name">Page Name</label>
                                                    <input type="text" name="page_name" id="page_name"
                                                        class="form-control" value="{{ $page->page_name }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" name="name" id="name" class="form-control"
                                                        value="{{ $page->name }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="content">Content</label>
                                                    <textarea name="content" id="summary-ckeditor" class="form-control" required>{{ $page->content }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="image">Image</label>
                                                    <input class="form-control dropify" name="image" type="file"
                                                        id="image"
                                                        {{ $page->image ? 'data-default-file=' . asset($page->image) : '' }}
                                                        {{ $page->image ? '' : 'required' }} value="{{ $page->image }}">
                                                </div>
                                            </div>

                                            @foreach ($page->sections as $section)
                                                <div class="col-md-12 section-block" id="section-{{ $section->id }}">
                                                    <div class="form-group">
                                                        <label>{{ $section->label }}</label>

                                                        @if ($section->type == 'image')
                                                            <input type="file" name="{{ $section->slug }}"
                                                                class="dropify"
                                                                data-default-file="{{ asset($section->value) }}">
                                                        @elseif($section->type == 'textarea')
                                                            <textarea name="{{ $section->slug }}" id="costom-summary-ckeditor-{{ $section->id }}">{{ $section->value }}</textarea>
                                                            @push('js')
                                                                <script>
                                                                    if ($('#costom-summary-ckeditor-{{ $section->id }}').length) {
                                                                        CKEDITOR.replace('costom-summary-ckeditor-{{ $section->id }}');
                                                                    }
                                                                </script>
                                                            @endpush
                                                        @elseif($section->type == 'video')
                                                            <img alt="" class="img-responsive mb-2"
                                                                src="{{ asset($section->value) }}" style="width: 30%;">
                                                            <input type="file" name="{{ $section->slug }}"
                                                                class="dropify"
                                                                {{ $section->value ? 'data-default-file=' . asset($section->value) : '' }}>
                                                        @else
                                                            <input type="text" name="{{ $section->slug }}"
                                                                value="{{ $section->value }}" class="form-control">
                                                        @endif

                                                        <!-- 🗑️ Delete Section Button -->
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm mt-1 deleteSection"
                                                            data-id="{{ $section->id }}">
                                                            <i class="la la-trash"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success mb-2" data-toggle="modal"
                                                    data-target="#addSectionModal">
                                                    + Add New Section
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions text-right pb-0">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="la la-check-square-o"></i> Edit
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

    <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSectionModalLabel">Add New
                        Section</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addSectionForm">
                        @csrf
                        <div class="form-group">
                            <label>Label</label>
                            <input type="text" name="label" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Slug (unique)</label>
                            <input type="text" name="slug" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add
                            Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('plugins/vendors/dropify/dist/js/dropify.min.js') }}"></script>
    <script>
        $(function() {
            // Initialize dropify for existing inputs
            $('.dropify').dropify();

            // Setup CSRF for all AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ✅ Function to initialize CKEditor + Dropify dynamically
            function initializePlugins() {
                // ✅ Reinitialize Dropify
                $('.dropify').dropify();

                // ✅ Reinitialize CKEditor safely for all matching textareas
                $('textarea[id^="costom-summary-ckeditor-"]').each(function() {
                    let editorId = $(this).attr('id');

                    // Avoid re-initialization if already done
                    if (!CKEDITOR.instances[editorId]) {
                        CKEDITOR.replace(editorId);
                    }
                });
            }

            // ✅ Add Section via AJAX
            $('#addSectionForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = form.serialize();
                const pageId = {{ $page->id }};
                let pageUrl = "{{ route('admin.sections.store', ['page' => ':id']) }}".replace(':id',
                    pageId);

                $.ajax({
                    url: pageUrl,
                    method: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#addSectionModal').modal('hide');
                            form.trigger('reset');

                            showToast(res.message, 'success');

                            // ✅ Build new section block
                            let html = `
                                <div class="col-md-12 section-block" id="section-${res.section.id}">
                                    <div class="form-group">
                                        <label>${res.section.label}</label>`;

                                    if (res.section.type === 'text') {
                                        html +=
                                            `<input type="text" name="${res.section.slug}" class="form-control">`;
                                    } else if (res.section.type === 'textarea') {
                                        html +=
                                            `<textarea name="${res.section.slug}" class="form-control" id="costom-summary-ckeditor-${res.section.id}"></textarea>`;
                                    } else if (res.section.type === 'image' || res.section.type ===
                                        'video') {
                                        html +=
                                            `<input type="file" name="${res.section.slug}" class="dropify">`;
                                    }

                                    html += `
                                        <button type="button" class="btn btn-danger btn-sm mt-1 deleteSection"
                                                data-id="${res.section.id}">
                                            <i class="la la-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>`;

                            // ✅ Insert above the Add Section button
                            $('.form-body .row .col-md-12:last').before(html);

                            // ✅ Re-initialize plugins for new section
                            initializePlugins();
                        }
                    },
                    error: function(err) {
                        showToast(err.responseJSON?.message || 'Something went wrong', 'error');
                    }
                });
            });

            // ✅ Delete Section via AJAX
            $(document).on('click', '.deleteSection', function() {
                const id = $(this).data('id');
                if (!confirm('Are you sure you want to delete this section?')) return;

                let sectionUrl = "{{ route('admin.sections.destroy', ['section' => ':id']) }}".replace(
                    ':id', id);

                $.ajax({
                    url: sectionUrl,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#section-' + id).remove();
                            showToast(res.message, 'info');
                        }
                    },
                    error: function() {
                        showToast('Unable to delete section', 'error');
                    }
                });
            });

            // Initialize CKEditor + Dropify on page load
            initializePlugins();
        });
    </script>
@endpush
