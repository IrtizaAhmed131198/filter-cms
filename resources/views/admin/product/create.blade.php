@extends('layouts.app')
@push('before-css')
    <link rel="stylesheet" href="{{ asset('plugins/vendors/dropify/dist/css/dropify.min.css') }}">
    <style>
        .tag {
            display: inline-flex;
            align-items: center;
            background: #0d6efd;
            color: white;
            padding: 3px 8px;
            border-radius: 15px;
            margin: 2px;
            font-size: 14px;
        }

        .tag .remove-tag {
            margin-left: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .tag-error {
            background: #dc3545 !important;
            color: white;
        }

        #tags-input {
            border: none;
            outline: none;
            padding: 5px;
            min-width: 120px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 55px;
            height: 28px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 28px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #0d6efd;
        }

        input:checked+.slider:before {
            transform: translateX(27px);
        }
    </style>
@endpush
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2 breadcrumb-new">
            <h3 class="content-header-title mb-0 d-inline-block">Create New Product</h3>
            <div class="row breadcrumbs-top d-inline-block">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('admin/product') }}">Product Management</a></li>
                        <li class="breadcrumb-item active">Create New Product</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="basic-form-layouts">
            <form class="form" enctype="multipart/form-data" method="post" action="{{ route('admin.banner.store') }}">
                @csrf
                <div class="row match-height">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form">Product Info</h4>
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
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Product Name</label>
                                                    <input class="form-control" required name="name" type="text"
                                                        id="name" value="{{ old('name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sku">SKU</label>
                                                    <input class="form-control" required name="sku" type="text"
                                                        id="sku" value="{{ old('sku') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="slug">Slug</label>
                                                    <input class="form-control" required name="slug" type="text"
                                                        id="slug" value="{{ old('slug') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="summary-ckeditor">Description</label>
                                                    <textarea name="description" id="summary-ckeditor" cols="30" rows="10" class="form-control" required>{{ old('description') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form">Product Image</h4>
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
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="summary-ckeditor">Image</label>
                                                    <div class="upload-photo">
                                                        <input class="form-control dropify" name="image" type="file"
                                                            id="image">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="summary-ckeditor">Gallery Image</label>
                                                    <div class="upload-photo">
                                                        <input class="form-control dropify" name="images[]"
                                                            type="file" id="images" multiple>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form">Product Variations</h4>
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
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="repeater-default col-md-12">
                                                <div data-repeater-list="product_attributes">
                                                    <div data-repeater-item class="row">

                                                        <div class="form-group col-md-3">
                                                            <label>Attribute</label>
                                                            <select class="form-control attribute_id" name="attribute_id"
                                                                onchange="getval(this)">
                                                                @foreach ($attributes as $atts)
                                                                    <option value="{{ $atts->id }}">{{ $atts->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label>Value</label>
                                                            <select class="form-control value" name="value"></select>
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label>Price</label>
                                                            <input type="number" class="form-control" name="price">
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label>Qty</label>
                                                            <input type="number" class="form-control" name="qty">
                                                        </div>

                                                        <div class="form-group col-md-2 mt-2">
                                                            <button type="button" class="btn btn-danger"
                                                                data-repeater-delete>
                                                                Delete
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="form-group overflow-hidden">
                                                    <div class="">
                                                        <button type="button" data-repeater-create=""
                                                            class="btn btn-primary">
                                                            <i class="ft-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions text-right pb-0">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="la la-check-square-o"></i> Add Product
                                        </button>
                                    </div>
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
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form">Pricing</h4>
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
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="base_price">Base Price</label>
                                                    <input class="form-control" required="required" name="base_price"
                                                        type="number" step="any" id="base_price"
                                                        value="{{ old('base_price') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="discount_price">Discount Price</label>
                                                    <input class="form-control" name="discount_price" type="number"
                                                        step="any" id="discount_price"
                                                        value="{{ old('discount_price') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 ml-1">
                                                <label>Charge tax on this product</label><br>
                                                <label class="switch">
                                                    <input type="checkbox" name="is_charge_tax" id="is_charge_tax"
                                                        checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="col-md-6 ml-1">
                                                <label>Stock</label><br>
                                                <label class="switch">
                                                    <input type="checkbox" name="stock" id="stock" checked>
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form">Organize</h4>
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
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="category">Select Category</label>
                                                    <select class="form-control" required name="category_id"
                                                        id="category">
                                                        <option value="" disabled selected>Select Category</option>
                                                        @foreach ($categories as $item)
                                                            <option value="{{ $item->id }}"
                                                                {{ old('category_id') == $item->id ? 'selected' : '' }}>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="subcat-container"
                                                style="{{ old('sub_category_id') ? '' : 'display:none;' }}">
                                                <div class="form-group">
                                                    <label for="subcategory">Select Sub Category</label>
                                                    <select class="form-control" name="sub_category_id" id="subcategory">
                                                        <option value="" disabled
                                                            {{ old('sub_category_id') ? '' : 'selected' }}>Select Sub
                                                            Category</option>
                                                        @if (old('category_id'))
                                                            @php
                                                                $subcategories = \App\Models\SubCategory::where(
                                                                    'category_id',
                                                                    old('category_id'),
                                                                )->get();
                                                            @endphp
                                                            @foreach ($subcategories as $sub)
                                                                <option value="{{ $sub->id }}"
                                                                    {{ old('sub_category_id') == $sub->id ? 'selected' : '' }}>
                                                                    {{ $sub->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="tags-input">Tags</label>
                                                    <div class="tags-box border p-2 rounded" id="tags-box">
                                                        <input type="text" class="tags-input" id="tags-input" placeholder="Type and press Enter">
                                                    </div>
                                                    <input type="hidden" name="tags" id="tags-hidden" value="{{ old('tags') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
@push('js')
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('plugins/vendors/dropify/dist/js/dropify.min.js') }}"></script>
    <script>
        $(function() {
            $('.dropify').dropify();
        });
        ! function(e, t, r) {
            "use strict";
            r(".repeater-default").repeater(), r(".file-repeater, .contact-repeater").repeater({
                show: function() {
                    r(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to remove this item?") && r(this).slideUp(e)
                }
            })
        }(window, document, jQuery);

        function getval(sel) {
            var globelsel = sel;
            let value = sel.value;

            // alert(value);

            $.ajax({
                url: "{{ route('get-attributes') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    value: value
                },
                success: function(response) {
                    $(globelsel).parent().parent().find('.value').html('');
                    if (response.status) {
                        var html = '';
                        for (var i = 0; i < response.message.length; i++) {
                            html += '<option value="' + response.message[i].id + '">' + response.message[i]
                                .value + '</option>';
                        }
                        $(globelsel).parent().parent().find('.value').html(html);
                    } else {

                    }
                },
            });
        }

        $('#category').on('change', function() {
            var category_id = $(this).val();
            var url = "{{ route('admin.product.getSubcategories', ':id') }}";
            url = url.replace(':id', category_id);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {

                    if (data.length > 0) {
                        $('#subcategory').empty();
                        $('#subcategory').append(
                            '<option value="" disabled selected>Select Sub Category</option>');

                        $.each(data, function(index, subcat) {
                            $('#subcategory').append('<option value="' + subcat.id + '">' +
                                subcat.name + '</option>');
                        });

                        $('#subcat-container').show(); // show subcategory box
                    } else {
                        $('#subcat-container').hide(); // hide if no subcategories
                    }
                }
            });
        });

        let tags = [];
        const tagInput = document.getElementById('tags-input');
        const tagBox = document.getElementById('tags-box');
        const hiddenField = document.getElementById('tags-hidden');

        // Load old tags
        @if(old('tags'))
            let oldTags = @json(explode(',', old('tags')));
            oldTags.forEach(t => addTag(t));
        @endif

        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let value = tagInput.value.trim();
                if (value === "") return;

                if (tags.includes(value.toLowerCase())) {
                    showDuplicateError(value);
                    tagInput.value = "";
                    return;
                }

                addTag(value);
                tagInput.value = "";
            }
        });

        function addTag(text) {
            tags.push(text.toLowerCase());

            const tag = document.createElement('span');
            tag.classList.add('tag');
            tag.innerHTML = `${text}<span class="remove-tag">&times;</span>`;

            tag.querySelector('.remove-tag').addEventListener('click', function() {
                tag.remove();
                tags = tags.filter(t => t !== text.toLowerCase());
                hiddenField.value = tags.join(',');
            });

            tagBox.insertBefore(tag, tagInput);
            hiddenField.value = tags.join(',');
        }

        function showDuplicateError(text) {
            const errorTag = document.createElement('span');
            errorTag.classList.add('tag', 'tag-error');
            errorTag.innerHTML = text;
            tagBox.insertBefore(errorTag, tagInput);
            setTimeout(() => errorTag.remove(), 1200);
        }
    </script>
@endpush
