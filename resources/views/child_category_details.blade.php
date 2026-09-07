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

	<main class="main__content_wrapper">
        
        <!-- Start breadcrumb section -->
        <section class="breadcrumb__section breadcrumb__bg">
            <div class="container">
                <div class="row row-cols-1">
                    
                    <div class="col">
                        <div class="breadcrumb__content text-center">
                            <ul class="breadcrumb__content--menu d-flex justify-content-center">
                                <li class="breadcrumb__content--menu__items"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb__content--menu__items"><span>{{$child_category->Name}}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End breadcrumb section -->

        <!-- Start portfolio section -->
        <section class="portfolio__section section--padding">
                <div class="container-fluid mt-4">

                <div class="row row-md-reverse">
                    
                    <div class="col-xl-3 col-lg-4">
                        <div class="shop__sidebar--widget widget__area">
                           
                            <div class="single__widget widget__bg">
                                <h2 class="widget__title h3">Categories</h2>
                                <ul class="widget__form--check">
                                    <li class="widget__form--check__list">
                                        <label class="widget__form--check__label" for="check1">Gallium Nitride (GaN) Devices</label>
                                        
                                    </li>
                                    <li class="widget__form--check__list">
                                        <label class="widget__form--check__label" for="check2"> Handling/Packing/Storage</label>
                                        
                                    </li>
                                    <li class="widget__form--check__list">
                                        <label class="widget__form--check__label" for="check3">Inductors, Coils, Chokes</label>
                                        
                                    </li>
                                    <li class="widget__form--check__list">
                                        <label class="widget__form--check__label" for="check4"> Labor protection supplies</label>
                                        
                                    </li>
                                    <li class="widget__form--check__list">
                                        <label class="widget__form--check__label" for="check5">Magnetic Sensors</label>
                                       
                                    </li>
                                </ul>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-xl-9 col-lg-8">  
                                 <div class="row row-border"> 

                                    <h3> <a href="{{url('/category')}}/{{ preg_replace('/[^\w\/]/', '-', strtolower($child_category->Name))}}/{{$child_category->id}}" class="fs-2" > {{$child_category->Name}} (<small>{{$child_category->ProductCount}}</small>) </a> </h3>  
                                    <ul class="products">
                                       {!! App\Common::get_sub_category($child_category->id) !!} 
                                   </ul> 
                                </div>  
                        </div>
                    </div>
                </div>
        </section>
        <!-- End portfolio section -->

        <!-- Start shipping section -->
        <section class="shipping__section">
            <div class="container">
                <div class="shipping__inner style2 d-flex justify-content-center">
                    
                    <div class="shipping__items style2 d-flex align-items-center">
                        <div class="shipping__icon">  
                            <img src="{{ url('public/assets/front/img/other/shipping2.webp') }}" alt="icon-img">
                        </div>
                        <div class="shipping__content">
                            <h4 class="shipping__content--title h4">Support 24/7</h4>
                            <p class="shipping__content--desc">Contact us 24 hours a day</p>
                        </div>
                    </div>
                    
                    <div class="shipping__items style2 d-flex align-items-center">
                        <div class="shipping__icon">  
                            <img src="{{ url('public/assets/front/img/other/shipping4.webp') }}" alt="icon-img">
                        </div>
                        <div class="shipping__content">
                            <h4 class="shipping__content--title h4">Payment Secure</h4>
                            <p class="shipping__content--desc">We ensure secure payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End shipping section -->
    </main>

@stop


@section('footer')
@stop