@extends('includes.front') 

@section('seo')


<title>Get Quote - {{$settings->meta_title}}</title>
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
                            <h1 class="breadcrumb__content--title mb-25"> BOM Upload</h1>
                            <ul class="breadcrumb__content--menu d-flex justify-content-center">
                                <li class="breadcrumb__content--menu__items"><a href="{{url('/')}}">Home</a></li>
                                <li class="breadcrumb__content--menu__items"><span>BOM Upload</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End breadcrumb section -->

        <!-- cart section start -->
        <section class="cart__section section--padding">
            <div class="container">
                <div class="cart__section--inner">
                <form action="{{route('get_a_quote_submit')}}" method="post"  class="">
                    @csrf
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                
                                   
                                    <div class="account__login">
                                    <div class="account__login--header mb-25">
                                        <h2 class="account__login--header__title mb-10">Get Quote</h2>
                                         <p class="account__login--header__desc" style="margin: 0px;padding: 0px;">Product Information:  {{$name}} <small> ({!! $details !!})</small></p>
                                       
                                    </div>

                                    <div class="col-md-12">
                                        @if(Session::has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        {{ Session::get('success') }}
                                        </div>
                                        @endif
                                    </div>

                                    <div class="col-md-12">
                                        @if(Session::has('fail'))
                                        <div class="alert alert-danger alert-dismissable">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        {{ Session::get('fail') }}
                                        </div>
                                        @endif
                                    </div>



                                    <div class="account__login--inner">

                                       <label class="d-block">
                                            <input class="account__login--input" name="name" autocomplete="off" required  placeholder="Name" type="text">
                                        </label>
                                     
                                        <label class="d-block">
                                            <input class="account__login--input"  name="email" autocomplete="off"  required placeholder="Email Address" type="email">
                                        </label class="d-block">
                                        <label class="d-block">
                                            <input class="account__login--input"  name="phone" autocomplete="off" required  placeholder="Contact Number" type="number">
                                        </label>
                                        <label class="d-block">
                                            <input class="account__login--input" name="company" autocomplete="off"  required  placeholder="Company Name" type="text">
                                        </label>
                                        <label class="d-block">
                                            <input class="account__login--input"  name="part_number" autocomplete="off"  value="{{$name}}" required  placeholder="Part Number" type="text">
                                        </label>
                                        <label class="d-block">
                                            <input class="account__login--input"  name="quantity"   autocomplete="off" required  placeholder="Quantity" type="number">
                                        </label> 
                                        <label class="d-block">
                                            <textarea class="account__login--input"  name="comments"  placeholder="Type your comment"  ></textarea>
                                        </label> 
                                        </div>
                                        <button class="account__login--btn primary__btn" type="submit">Submit</button>
                                       </div>
                                </div>
                                    
                                
                            </div>
                            <div class="col-lg-3"></div>
                        </div> 
                    </form> 
                </div>
            </div>     
        </section>
        <!-- cart section end -->
    </main>

@stop 

@section('footer') 

@stop