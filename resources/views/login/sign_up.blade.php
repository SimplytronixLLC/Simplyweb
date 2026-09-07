  

@extends('includes.front')



@section('seo')	 

    <title>Sign Up | {{@$settings->meta_title}}</title>

    <meta name="title" content="Sign Up - {{@$settings->meta_title}}">

    <meta name="description" content="{{@$settings->meta_description}}" />	

    <meta name="keywords" content="{{@$settings->meta_keyword}}" />	

    <meta property="og:locale" content="en_US" />	

    <meta property="og:site_name" content="Sign Up - {{@$settings->meta_title}}" />

    <meta property="og:type" content="article" />

    <meta property="og:title" content="Sign Up - {{@$settings->meta_title}}" />

    <meta property="og:description" content="{{@$settings->meta_description}}" />

    <meta property="og:url" content="<?php echo url()->current();  ?>" />

    <meta property="og:image" content="{{ url('public/uploads')}}/{{@$settings->logo}}" />

    <meta property="og:image:secure_url" content="{{ url('public/uploads')}}/{{@$settings->logo}}" />

    <meta property="og:image:width" content="680" />



@stop	





@section('content')

  	  
 <h1 style="text-align: center;padding: 30px;background: #ffffff;    box-shadow: inset 0px 0px 5px 3px #8d8e8f; " >Welcome to Sign Up - {{@$settings->meta_title}} </h1>

@stop



@section('footer')



@stop