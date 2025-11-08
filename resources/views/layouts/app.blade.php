<!DOCTYPE html>
<html lang="en">
<head>

	<?php
	   $favicon = DB::table('imagetable')->where('table_name', 'favicon')->first();
	?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Mintone">
    <meta name="author" content="Admin Mintone">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset(!empty($favicon->img_path)?$favicon->img_path:'')}}">
    <title>{{ config('app.name') }} - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors.min.css')}}">
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-extended.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/colors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/components.min.css')}}">
    <!-- END: Theme CSS-->
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vertical-menu-modern.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/palette-gradient.min.css')}}">
    <!-- END: Page CSS-->
    <link href="{{asset('plugins/vendors/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/vendors/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/datatables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.3.3/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .select2-tags-input .select2-container--default .select2-selection--multiple .select2-selection__rendered li {
            padding: 1px 11px 0px 24px !important;
        }

        .select2-tags-input button.select2-selection__choice__remove {
            padding: 1px 4px !important;
        }

        .select2-tags-input button.select2-selection__choice__remove:hover {
            background-color: #666ee8 !important;
        }
    </style>
    @stack('before-css')
    <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet">
   <script src="{{ asset('vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>

    @stack('after-css')

</head>


<body class="vertical-layout vertical-menu-modern 2-columns   fixed-navbar" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css')}} -->
<!-- ============================================================== -->
@include('layouts.admin.header')

@include('layouts.admin.sidebar')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        @yield('content')
    </div>
</div>

@include('layouts.admin.footer')

<!-- BEGIN: Vendor JS-->
<script src="{{asset('assets/js/vendors.min.js')}}"></script>
<!-- BEGIN Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{asset('assets/js/app-menu.min.js')}}"></script>
<script src="{{asset('assets/js/app.min.js')}}"></script>
<script src="{{asset('assets/js/customizer.min.js')}}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
<script src="https://cdn.datatables.net/rowreorder/1.3.3/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- END: Theme JS-->

<script src="{{ asset('js/crud-manager.js') }}"></script>
<script src="{{ asset('js/toast.js') }}"></script>

<script>
    if($('#summary-ckeditor').length != 0){
        CKEDITOR.replace( 'summary-ckeditor' );
    }
    if($('#summary-ckeditor1').length != 0){
        CKEDITOR.replace( 'summary-ckeditor1' );
    }
    if($('#summary-ckeditor2').length != 0){
        CKEDITOR.replace( 'summary-ckeditor2' );
    }
</script>



<script>

	 $(document).ready(function () {

            @if(\Session::has('message'))
            $.toast({
                heading: 'Success!',
                position: 'top-center',
                text: '{{session()->get('message')}}',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
            @endif


            @if(\Session::has('flash_message'))
            $.toast({
                heading: 'Info!',
                position: 'top-center',
                text: '{{session()->get('flash_message')}}',
                loaderBg: '#ff6849',
                icon: 'error',
                hideAfter: 3000,
                stack: 6
            });
            @endif


        });


</script>

<!-- ============================================================== -->
@stack('js')

</body>
</html>
