@extends('layouts.main')
@section('title', 'Order')
@section('content')

<?php $segment = Request::segments(); ?>


<section class="banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="banner-wrapper inner-banner-wrapper">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="section-heading text-center">
                                <h1>Orders</h1>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<main class="my-cart">
  
 <!-- my account wrapper start -->
    <div class="my-account-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- My Account Page Start -->
                    <div class="myaccount-page-wrapper">
                        <!-- My Account Tab Menu Start -->
                        <div class="row">
                            @include('account.sidebar')
                            <!-- My Account Tab Menu End -->
    
                            <!-- My Account Tab Content Start -->
                            <div class="col-lg-9 col-md-8">
                                <div class="tab-content" id="myaccountContent">
                                   
                                    <!-- Single Tab Content Start -->
                                    <div class="tab-pane fade show active" id="orders" role="#">
                                        <div class="myaccount-content">
                                            <div class="section-heading">
                                                <h2>Orders</h2>
                                            </div>
    
                                            <div class="myaccount-table table-responsive text-center">
                                                <table class="table table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Invoice Number</th>
                                                            <th>Date</th>
                                                            <th>Total</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
    
                                                    <tbody>
                                                    
                                                    @if($ORDERS)
                                                        @foreach($ORDERS as $ORDER)
                                                            <tr>
                                                              <td>{{ $ORDER->id }}</td>
                                                             
                                                              <td>{{ $ORDER->invoice_number }}</td>
                                                              <td>{{date('d F, Y h:i a',strtotime($ORDER->created_at))}}</td>
                                                              <td>${{ $ORDER->order_total  }}</td>
                                                              <td class="viewbtn"><a href="{{ route('invoice',[$ORDER->id]) }}">View</a></td>
                                                              
                                                            </tr>
                                                        @endforeach
                                                    @endif
                    
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Single Tab Content End -->
    
                                    
                                </div>
                            </div> <!-- My Account Tab Content End -->
                        </div>
                    </div> <!-- My Account Page End -->
                </div>
            </div>
        </div>
    </div>
    <!-- my account wrapper end -->


<!-- main content end -->   
</main
>

@endsection
@section('css')
<style type="text/css">
    
</style>
@endsection
@section('js')
<script type="text/javascript">
     $(document).on('click', ".btn1", function(e){
            // alert('it works');
            $('.loginForm').submit();
     });
</script>
@endsection