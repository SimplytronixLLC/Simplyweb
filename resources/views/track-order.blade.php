@extends('includes.front')

@section('seo')

    <title>{{$settings->meta_title}}</title> 
        <meta data-rh="true" name="title" content="{{$settings->meta_title}}">
        <meta data-rh="true" name="keywords" content="{{$settings->meta_keyword}}">
        <meta data-rh="true" name="description" content="{{$settings->meta_description}}">
        <meta data-rh="true" name="language" content="en">
         
        <meta data-rh="true" property="og:url" content="{{url('/')}}">
        <meta data-rh="true" property="og:site_name" content="{{$settings->meta_title}}">
        <meta data-rh="true" property="og:type" content="website">
        <meta data-rh="true" property="og:title" content="{{$settings->meta_title}}">
        <meta data-rh="true" property="og:description" content="{{$settings->meta_description}}">
        <meta data-rh="true" property="og:image" content="{{url('public')}}/{{$settings->logo}}">

        <meta data-rh="true" name="twitter:title" content="{{$settings->meta_title}}">
        <meta data-rh="true" name="twitter:site" content="@yourTwitterHandle">
        <meta data-rh="true" name="twitter:description" content="{{$settings->meta_description}}">
        <meta data-rh="true" name="twitter:creator" content="">
        <meta data-rh="true" name="twitter:card" content="summary_large_image">
        <meta data-rh="true" name="twitter:image:src" content="{{url('public')}}/{{$settings->logo}}">
        <meta data-rh="true" name="twitter:image" content="{{url('public')}}/{{$settings->logo}}">

@stop

@section('content')

          <!-- Start breadcrumb section -->
        <section class="breadcrumb__section breadcrumb__bg bg-white">
            <div class="container">
                <div class="row row-cols-1">
                    
                    <div class="col">
                        <div class="breadcrumb__content text-center">
                            <ul class="breadcrumb__content--menu d-flex justify-content-center">
                                <li class="breadcrumb__content--menu__items"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb__content--menu__items"><span>Track Your Order</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End breadcrumb section -->

            <!-- Start product details section -->
        <section class="product__details--section pt-5 bg-gray pb-5">
            <div class="container ">
                <div class="row row-cols-lg-2 row-cols-md-2 ">

                    <div class="col-lg-3">
                    </div>
                    
                      <div class="col-lg-6 offset-lg-0 col-md-6 offset-md-0 ">
                            <div class="product__sidebar small__product position-relative">
                                <div class="small__product--header">
                                    <h2 class="small__product--header__title text-center ">Enter Your Order ID</h2>

                                </div>
                                
                                          <div class="p-5">

                                            <div class="main checkout__mian">

                                                    @include('alerts')
                                                    <form action="{{route('submit_track_order')}}" method="POST" data-parsley-validate>
                                                    @csrf
                                                    <div class="checkout__content--step section__contact--information">
                                                        <div class="section__header checkout__section--header d-flex align-items-center justify-content-between mb-25">
                                                        </div>
                                                        <div class="customer__information">
                                                            <div class="checkout__email--phone mb-12 d-flex align-items-center">
                                                                <input class="checkout__input--field border-radius-5 track-input" 
                                                                       placeholder="Track Your Order" 
                                                                       type="text" 
                                                                       name="order_id" 
                                                                       required 
                                                                       data-parsley-required-message="Order ID is required"
                                                                       data-parsley-length="[3, 20]"
                                                                       data-parsley-length-message="Order ID should be between 3 and 20 characters">
                                                                <button type="submit" class="btn tracknow__button">Track Now</button>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </form>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                    </div>
                </div>
            </section>

           @if($quotation)

        <section class="product__details--section pt-5 pb-5 bg-gray">
            <div class="container ">
                <div class="row row-cols-lg-2 row-cols-md-2 ">
                     
                        <div class="col-lg-3">
                        </div>

                        <div class="col-lg-6 offset-lg-0 col-md-6 offset-md-0">
                            <div class="product__sidebar small__product position-relative">
                                <div class="small__product--header">
                                    <h2 class="small__product--header__title text-center">Quotation Info.</h2>
                                </div>
                                <div class="small__product--inner product__swiper--column1 swiper">
                                    <div class="swiper-wrapper">
                                        <div class="">

                                           <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Order Id:</strong>  <span>{{$quotation->order_id}}</span> </p>

                                           <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Name:</strong>  <span>{{$quotation->name}}</span> </p>

                                           <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Email:</strong>  <span>{{$quotation->email}}</span> </p>

                                           <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Phone:</strong>  <span>{{$quotation->phone}}</span> </p>

                                           <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Part Number:</strong>  <span>{{$quotation->company}}</span> </p>

                                           <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Part Number:</strong>  <span>{{$quotation->part_number}}</span> </p>

                                            <p class="product__details--info__meta--list p-3 mb-1 border-bottom border-2"><strong>Quantity:</strong>  <span>{{$quotation->quantity}}</span> </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                        </div>


                     </div>
                </section>

                @endif 




@stop

@section('footer')

@stop