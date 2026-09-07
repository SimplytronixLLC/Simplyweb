@extends('includes.front')

@section('content')

<section style="background:#f8fafc; padding:30px 0;">
<div class="container">

<h2 style="font-size:20px; font-weight:600; margin-bottom:20px;">
{{ $category->name }}
</h2>

<div class="row">
@foreach($children as $child)
    <div class="col-md-3 col-sm-6 mb-3">
        <a href="{{ url('/category/'.$child->slug) }}"
           style="text-decoration:none;">

            <div style="
                background:#ffffff;
                padding:14px;
                border-radius:8px;
                box-shadow:0 2px 8px rgba(0,0,0,0.04);
                text-align:center;
                font-size:14px;
                font-weight:500;
                color:#1f2937;
                transition:all 0.2s ease;
            "
            onmouseover="this.style.boxShadow='0 4px 14px rgba(0,0,0,0.08)'"
            onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">

                {{ $child->name }}

            </div>
        </a>
    </div>
@endforeach
</div>

</div>
</section>

@endsection