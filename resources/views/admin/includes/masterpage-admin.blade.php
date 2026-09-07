<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
     <meta name="csrf-token" content="{{ csrf_token() }}" />
	<link rel="icon" type="image/png" href="{{url('/')}}/public/uploads/admin/{{ Auth::user()->photo}}"/>
    
    @hasSection('seo')
          @yield('seo')
    @else
        <title>Admin Dashboard - {{@$settings->title}}</title> 
        <meta   name="title" content="Admin Dashboard  - {{@$settings->title}}">
        <meta  name="keywords" content="Admin Dashboard  - {{@$settings->title}}">
        <meta  name="description" content=" Admin Dashboard  - {{@$settings->title}}"> 
    @endif

    <link rel="canonical" href="<?php echo url()->current();  ?>"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('public/assets/admin/vendor/chartist/css/chartist.min.css')}}">    
    <link href="{{ URL::asset('public/assets/admin/vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/admin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}" rel="stylesheet">    
    <link href="{{ URL::asset('public/assets/parsley/parsley.css')}}" rel="stylesheet"> 
    <link href="{{ URL::asset('public/assets/admin/css/style.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/admin/css/style-2.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('public/assets/toastr/toastr.css')}}" />
  

<style>

:root{
    --sx-sidebar-width:16rem;
    --sidebar-bg:#ffffff;
    --sidebar-text:#1A1F2B;
    --sidebar-muted:#6B7280;
    --sidebar-hover:#EEF4FF;
    --sidebar-active:#E6F0FF;
    --sidebar-border:#E8EDF5;
    --sidebar-primary:#4F46E5;
}

/* =========================
   SIDEBAR
========================= */

.nav-header .brand-title{
    margin-left:23px;
    max-width:200px;
    height:50px;
}

@media (min-width: 768px) {
    .deznav{
        width:var(--sx-sidebar-width)!important;
        padding-top:70px;
        background:var(--sidebar-bg);
    }

    .nav-header{
        width:var(--sx-sidebar-width)!important;
        height:4.5rem;
        background:#eaf8ff;
        box-shadow:-7px -1px 3px 3px rgba(0,0,0,.05);
    }

    [data-layout="vertical"] .content-body{
        margin-left:var(--sx-sidebar-width)!important;
    }

    [data-layout="vertical"] .header{
        padding-left:var(--sx-sidebar-width)!important;
    }
}

[data-sidebar-style="full"][data-layout="vertical"] .deznav .metismenu>li{
    padding:0;
    margin:2px 10px;
}

/* =========================
   MAIN MENU
========================= */

[data-sidebar-style="full"][data-layout="vertical"] .deznav .metismenu>li>a{

    display:flex;
    align-items:center;
    gap:12px;

    font-family:'Poppins',sans-serif;
    font-size:13px;
    font-weight:600;

    color:var(--sidebar-text);

    padding:11px 14px;

    border-radius:10px;

    border:none;

    transition:.2s ease;
}

.deznav .metismenu>li>a:hover{
    background:var(--sidebar-hover);
    color:var(--sidebar-primary);
}

.deznav .metismenu>li.mm-active>a,
.deznav .metismenu>li.active>a{
    background:var(--sidebar-active);
    color:var(--sidebar-primary);
}

.menu-icon{
    width:20px;
    height:20px;
    flex-shrink:0;
    stroke:currentColor;
}

.deznav .metismenu svg{
    stroke:currentColor;
}

.nav-text{
    margin:0!important;
    font-family:'Poppins',sans-serif;
    font-size:13px;
    font-weight:600;
    letter-spacing:.1px;
}

/* =========================
   SUB MENU
========================= */

.deznav .metismenu ul{
    margin-top:4px;
    margin-bottom:6px;
}

.deznav .metismenu ul a{

    padding:8px 14px 8px 46px;

    font-family:'Poppins',sans-serif;
    font-size:12px;
    font-weight:500;

    color:var(--sidebar-muted);

    border:none;

    transition:.2s ease;
}

.deznav .metismenu ul a:hover{
    color:var(--sidebar-primary);
    background:#F8FAFF;
}

.deznav .metismenu ul li.mm-active>a{
    color:var(--sidebar-primary);
    background:#EEF4FF;
    border-radius:8px;
}

/* =========================
   HEADER
========================= */

.nav-control{
    top:50%;
}

.header{
    height:4.5rem;
}

.header .header-content{
    background:#fff;
    box-shadow:0 0 5px rgba(0,0,0,.08);
}

[data-header-position="fixed"] .content-body{
    padding-top:4rem;
}

/* =========================
   SIDEBAR HIDE/SHOW TOGGLE
========================= */

body.sx-sidebar-hidden .deznav{
    display:none!important;
}

body.sx-sidebar-hidden .nav-header{
    width:70px!important;
    background:transparent!important;
    box-shadow:none!important;
    display:flex!important;
    align-items:center!important;
    justify-content:center!important;
}
body.sx-sidebar-hidden .nav-header .brand-logo{
    display:none!important;
}
body.sx-sidebar-hidden .nav-header .nav-control{
    position:static!important;
    top:auto!important;
    left:auto!important;
    right:auto!important;
    transform:none!important;
    margin:0!important;
}

.content-body{
    transition:margin-left .2s ease;
}

[data-layout="vertical"] .header{
    transition:padding-left .2s ease;
}

@media (min-width: 768px) {
    body.sx-sidebar-hidden .content-body,
    body.sx-sidebar-hidden[data-layout="vertical"] .content-body,
    [data-layout="vertical"]body.sx-sidebar-hidden .content-body{
        margin-left:0!important;
    }
    body.sx-sidebar-hidden .header,
    body.sx-sidebar-hidden[data-layout="vertical"] .header,
    [data-layout="vertical"]body.sx-sidebar-hidden .header{
        padding-left:0!important;
    }
    body.sx-sidebar-hidden .content-body .container-fluid{
        max-width:100%!important;
    }
}

/* =========================
   OTHER
========================= */

.avtivity-card:hover .fs-20{
    color:#fff!important;
}

/* sx-sidebar-toggle: full-width override */
body.sx-sidebar-hidden .content-body {
    margin-left: 0 !important;
}
body.sx-sidebar-hidden .header {
    padding-left: 0 !important;
}
body.sx-sidebar-hidden .content-body .container-fluid {
    max-width: 100% !important;
}
</style>

</head>

<body>
<div id="preloader">
    <div class="sk-three-bounce">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
</div>
	  
	  
<div id="main-wrapper">
    <div class="nav-header">
        <a href="{!! url('admin/dashboard') !!}" class="brand-logo">
            <img class="brand-title" src="{{url('/')}}/public/uploads/{{@$settings->logo}}" >
        </a>

        <div class="nav-control" id="sxSidebarToggle" title="Toggle Sidebar" style="cursor:pointer;">
            <div class="hamburger">
                <span class="line"></span><span class="line"></span><span class="line"></span>
            </div>
        </div>
    </div>

    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">
                    <div class="header-left">
                        <div class="dashboard_bar"> Dashboard  </div>
                    </div>
                    <ul class="navbar-nav header-right">
                       <li class="nav-item "><a href="{!! url('/') !!}" class="btn btn-primary" target="_blank">View Website</a></li> 
                        <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0)" role="button" data-toggle="dropdown">
                                    <img src="{{url('/')}}/public/uploads/admin/{{ Auth::user()->photo}}" width="20" alt=""/>
                                    <div class="header-info">
                                        <span class="text-black"><strong>{{ Auth::user()->name }}</strong></span>
                                        <p class="fs-12 mb-0">  Admin</p>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{!! url('admin/adminprofile') !!}" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ml-2">Profile </span>
                                    </a>
                                    <a href="{!! url('admin/adminpassword') !!}" class="dropdown-item ai-icon">
                                        <svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        <span class="ml-2">Change Password </span>
                                    </a>
                                    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();  document.getElementById('logout-form').submit();" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ml-2">Logout </span>
                                    </a>
                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
    <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">
            <li class="nav-item  {{ (request()->is('admin/dashboard')) ? 'active' : '' }}">
                <a href="{!! url('admin/dashboard') !!}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg><span class="nav-text">Dashboard</span></a>
            </li>

            <li class="nav-item  {{ (request()->is('admin/products')) ? 'active' : '' }}">
                <a href="{!! url('admin/products') !!}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg><span class="nav-text">Products</span></a>
            </li>

           

            

            <li class="nav-item  {{ (request()->is('admin/quotation')) ? 'active' : '' }}">
                
                    <a href="{!! url('admin/quotation') !!}">
                   <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span class="nav-text">Quotation</span>
                </a> 
            </li>
            <li class="nav-item {{ request()->is('admin/visitors*') ? 'active' : '' }}">
                <a href="{{ route('admin.visitors.index') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span class="nav-text">Visitors</span>
                </a>
            </li>
            <li class="nav-item {{ request()->is('admin/leads*') ? 'active' : '' }}">
                <a href="{{ route('admin.leads.index') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg><span class="nav-text">Lead Intel</span>
                </a>
            </li>
            <li class="nav-item {{ request()->is('admin/leads/stock-alerts*') ? 'active' : '' }}">
                <a href="{{ route('leads.stock-alerts') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span class="nav-text">Stock Alerts</span>
                </a>
            </li>
            <li class="nav-item {{ request()->is('admin/crm*') ? 'active' : '' }}">
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="5" height="16"/><rect x="10" y="4" width="5" height="10"/><rect x="17" y="4" width="4" height="13"/></svg><span class="nav-text">CRM</span>
                </a>
                <ul aria-expanded="false">
                    <li class="{{ request()->is('admin/crm') ? 'mm-active' : '' }}">
                        <a href="{{ url('admin/crm') }}">Pipeline</a>
                    </li>
                    <li class="{{ request()->is('admin/crm/winback') ? 'mm-active' : '' }}">
                        <a href="{{ url('admin/crm/winback') }}">Win-back</a>
                    </li>
                    <li class="{{ request()->is('admin/crm/duplicates') ? 'mm-active' : '' }}">
                        <a href="{{ url('admin/crm/duplicates') }}">Duplicates</a>
                    </li>
                    <li class="{{ request()->is('admin/crm/bulk-email') ? 'mm-active' : '' }}">
                        <a href="{{ url('admin/crm/bulk-email') }}">Bulk Email</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item  {{ (request()->is('admin/registered-users')) ? 'active' : '' }}">
                    <a href="{!! url('admin/registered-users') !!}">
                   <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span class="nav-text">Registered Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="https://vendors.simplytronix.com/admin" target="_blank" rel="noopener">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span class="nav-text">Vendor Portal</span>
                </a>
            </li>
            			<li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                   <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="m2 13 10 5 10-5"/></svg><span class="nav-text">Pages</span>
                </a> 
                <ul aria-expanded="false"> 
                    <!--li><a href="{!! url('admin/slider') !!}">Slider</a></li--> 
                    <li><a href="{!! url('admin/about') !!}">About</a></li>  
                    <li><a href="{!! url('admin/privacy') !!}">Privacy Policy</a></li>
                    <li><a href="{!! url('admin/terms') !!}"> Terms & Conditions</a></li>
                </ul>
            </li>
			
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.82l.06.06-2 3.46-.08-.02a1.7 1.7 0 0 0-1.8.4l-.28.28-3.64-1.46-.05-.38a1.7 1.7 0 0 0-1.37-1.46"/></svg><span class="nav-text">General Options</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{!! url('admin/logo') !!}">Logo</a></li>
					 <li><a href="{{ route('admin.general-settings') }}">Settings</a></li>
                    </ul>
            </li>
            <li class="nav-item {{ request()->is('admin/api-usage') ? 'active' : '' }}">
                <a href="{{ url('admin/api-usage') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9-6-18-3 9H2"/></svg><span class="nav-text">API Usage</span>
                </a>
            </li>
            <li class="nav-item {{ request()->is('admin/sync*') ? 'active' : '' }}">
                <a href="{{ route('admin.sync.index') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6l3 2"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6l-3-2"/></svg><span class="nav-text">DigiKey Sync</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->is('admin/specs-editor') ? 'active' : '' }}">
                <a href="{{ route('admin.specs.index') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg><span class="nav-text">Specs Editor</span>
                </a>
            </li>
            <li class="nav-item {{ request()->is('admin/seo-tools') ? 'active' : '' }}">
            <a href="{{ url('admin/seo-tools') }}">
            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg><span class="nav-text">SEO Tools</span>
             </a>
            </li>
            <li class="nav-item {{ request()->is('admin/seo-audit*') ? 'active' : '' }}">
                <a href="{{ route('admin.seo.audit') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg><span class="nav-text">SEO Audit</span>
                </a>
            </li>
            <li class="nav-item {{ request()->is('admin/posts*') ? 'active' : '' }}">
                <a href="{{ route('admin.posts.index') }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h13a2 2 0 0 1 2 2v12H6a2 2 0 0 1-2-2z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg><span class="nav-text">Blog / News</span>
                </a>
            </li>
		    <li>
                <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg><span class="nav-text">Logout</span></a>
            </li>	
            
            
        </ul>
    </div>
</div>
<div class="content-body">
    <div class="container-fluid">
	   @yield('content')
	</div>
</div>
<div class="footer">
    <div class="copyright"> 
    </div>
</div>
	

	<!-- /#wrapper -->
	<script>
		var baseUrl = '{!! url(' / ') !!}';
 	</script>
   <script src="{{ URL::asset('public/assets/admin/vendor/global/global.min.js')}}"></script>
    <script src="{{ URL::asset('public/assets/admin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
    <!-- <script src="{{ URL::asset('public/assets/admin/vendor/chart.js/Chart.bundle.min.js')}}"></script> -->
    <script src="{{ URL::asset('public/assets/admin/js/custom.min.js')}}"></script>
    <script src="{{ URL::asset('public/assets/admin/js/deznav-init.js')}}"></script>    
    <script src="{{ URL::asset('public/assets/admin/vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ URL::asset('public/assets/admin/js/plugins-init/datatables.init.js')}}"></script>
    <script src="{{ URL::asset('public/assets/parsley/parsley.js')}}"></script>

    <script src="{{ url('public/assets/toastr/sweetalert.min.js')}}"></script>
    <script src="{{ url('public/assets/toastr/toastr.min.js')}}"></script>
    

    <!-- Chart piety plugin files -->
    <script src="{{ URL::asset('public/assets/admin/vendor/peity/jquery.peity.min.js')}}"></script> 
    <script src="https://cdn.tiny.cloud/1/{{ env("TINYMCE_API_KEY") }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
            
            
            tinymce.init({
                selector: '.summernote',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker code',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
                height: 400,
                menubar: 'file edit view insert format tools table help',
                branding: false,
                allow_html_in_named_anchor: true,
                extended_valid_elements: 'script[src|type],canvas[id|class],style[type]',
                custom_elements: 'style',
                valid_children: '+body[style],+body[script]',
                paste_data_images: true,
                setup: function(editor) {
                    editor.on('paste', function(e) {
                        return true;
                    });
                }
            }); 

   </script>

<script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })

         $('body').tooltip({selector: '[data-toggle="tooltip"]'});

         //on click preventDefault text options
          $(".number").bind("keypress", function (e) {
              var keyCode = e.which ? e.which : e.keyCode
                   
              if (!(keyCode >= 48 && keyCode <= 57)) {
                $(".numbererror").css("display", "inline");
                return false;
              }else{
                $(".numbererror").css("display", "none");
              }
          });
            $("li").on('click', function(e){
              //  $(this).addClass('current').siblings().removeClass('current'); 
            });
    </script>
	<script>


        function uploadclick(){
            $("#uploadFile").click();
            $("#uploadFile").change(function(event) {
                $("#uploadTrigger").html($("#uploadFile").val());
            });
        }
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#adminimg').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }


        function uploadclick1(){
            $("#uploadFile1").click();
            $("#uploadFile1").change(function(event) {
                $("#uploadTrigger1").html($("#uploadFile1").val());
            });
        }
        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#adminimg1').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }




	$(document).ready(function() {
			  setTimeout(function() {
				 $( ".tox-notifications-container" ).remove();
			  }, 1000);
	  });
 
 
        /* Encode string to slug */
        function convertToSlug(str) {
            document.getElementById("seoTitle").value =   str;
            //replace all special characters | symbols with a space
            str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ').toLowerCase();

            // trim spaces at start and end of string
            str = str.replace(/^\s+|\s+$/gm, '');

            // replace space with dash/hyphen
            str = str.replace(/\s+/g, '-');
            document.getElementById("slug").value = str;
            //return str;
        } 

</script>

  @yield('footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function startHarvest() {
    fetch('/admin/harvest/start', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

function stopHarvest() {
    fetch('/admin/harvest/stop', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

function updateHarvestStatus() {

    fetch('/admin/harvest/status')
    .then(res => res.json())
    .then(data => {

        if (!document.getElementById('h-status')) return;

        document.getElementById('h-status').innerText = data.is_running ? 'Running' : 'Stopped';
        document.getElementById('h-key').innerText = data.current_keyword || '-';
        document.getElementById('h-offset').innerText = data.current_offset || 0;
        document.getElementById('h-api').innerText = data.api_used || 0;
        if (document.getElementById('h-fetched')) document.getElementById('h-fetched').innerText = data.total_fetched || 0;
        if (document.getElementById('h-inserted')) document.getElementById('h-inserted').innerText = data.total_inserted || 0;
        if (document.getElementById('h-stopreason')) document.getElementById('h-stopreason').innerText = data.stop_reason || '-';
        if (document.getElementById('h-fetched')) document.getElementById('h-fetched').innerText = data.total_fetched || 0;
        if (document.getElementById('h-inserted')) document.getElementById('h-inserted').innerText = data.total_inserted || 0;
        if (document.getElementById('h-stopreason')) document.getElementById('h-stopreason').innerText = data.stop_reason || '-';

    }).catch(() => {});
}

setInterval(updateHarvestStatus, 1000);
</script>

<script>
(function(){
    var body = document.body;
    var toggleBtn = document.getElementById('sxSidebarToggle');
    var STORAGE_KEY = 'sx_sidebar_hidden';

    // custom.min.js binds its own click handler to .nav-control for the theme's
    // built-in mini-sidebar mode (toggles #main-wrapper.menu-toggle / .hamburger.is-active).
    // Our toggle button lives on that same element now, so strip that native handler
    // and any stale classes it left behind to stop the two mechanisms from fighting.
    if (window.jQuery) {
        jQuery('.nav-control').off('click');
        jQuery('#main-wrapper').removeClass('menu-toggle');
        jQuery('.hamburger').removeClass('is-active');
    }

    // restore state on load
    if (localStorage.getItem(STORAGE_KEY) === '1') {
        body.classList.add('sx-sidebar-hidden');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(){
            body.classList.toggle('sx-sidebar-hidden');
            localStorage.setItem(STORAGE_KEY, body.classList.contains('sx-sidebar-hidden') ? '1' : '0');
        });
    }
})();
</script>
</body>

</html>