@extends('includes.front')

@section('content')

<section style="background:#f8fafc; padding:30px 0;">
<div class="container">

<h2 style="font-size:20px; font-weight:600; margin-bottom:20px;">
{{ $category->name }}
</h2>

{{-- ✅ CASE 1: HAS CHILDREN --}}
@if(!empty($children) && count($children))

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

{{-- ✅ CASE 2: NO CHILDREN → SHOW PRODUCTS --}}
@else

@if(!empty($products) && count($products))

<div class="table-responsive">

<table class="table table-bordered" style="font-size:14px; background:#fff;">

<thead style="background:#f9fafb;">
<tr>
    <th>Part Number</th>
    <th>Description</th>
    <th>Manufacturer</th>
</tr>
</thead>

<tbody>
@foreach($products as $product)

@php
$slug = \Illuminate\Support\Str::slug($product->manufacturer);
$safePart = rawurlencode(str_replace(['/','#'],['__','--'],$product->product_key));
@endphp

<tr>

<td>
<a href="{{ url('product/'.$slug.'/'.$safePart) }}"
   style="color:#ea580c; font-weight:600; text-decoration:none;">
    {{ $product->product_key }}
</a>
</td>

<td>
{{ \Illuminate\Support\Str::limit($product->description ?? '', 120) }}
</td>

<td>
{{ $product->manufacturer ?? '' }}
</td>

</tr>

@endforeach
</tbody>

</table>

{{-- Pagination (if exists) --}}
@if(method_exists($products,'links'))
<div class="mt-3 text-center">
    {{ $products->links() }}
</div>
@endif

</div>

@else

<div style="padding:20px; background:#fff; border-radius:6px;">
No products available
</div>

@endif

@endif

</div>
</section>

@endsection