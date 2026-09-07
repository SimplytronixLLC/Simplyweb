@extends('includes.front')

@section('seo')
    <title>{{ $category->name }}</title>
    <meta name="description" content="Browse {{ $category->name }} products">
@endsection

@section('content')

<section style="background:#f8fafc; padding:30px 0;">
<div class="container-fluid">

<div style="
    background:#ffffff;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
">

<div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
    <h4 style="margin:0; font-size:18px; font-weight:600;">
        {{ $category->name }}
    </h4>
</div>

<div style="padding:18px;">

<div class="table-responsive">

<table class="table table-bordered" style="font-size:14px;">

<thead style="background:#f9fafb;">
<tr>
    <th>Image</th>
    <th>Part Number</th>
    <th>Description</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Manufacturer</th>
    <th>Quote</th>
</tr>

<tr style="background:#ffffff;">
    <th colspan="5"></th>

    <th>
        <form method="GET">
            <select name="manufacturer"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()"
                    style="font-size:13px;">
                <option value="">All</option>
                @foreach($manufacturers as $mfg)
                    <option value="{{ $mfg }}"
                        {{ request('manufacturer') == $mfg ? 'selected' : '' }}>
                        {{ $mfg }}
                    </option>
                @endforeach
            </select>
        </form>
    </th>

    <th class="text-center">
        <a href="{{ url()->current() }}"
           style="font-size:12px; color:#f97316; text-decoration:none; font-weight:500;">
            Reset
        </a>
    </th>
</tr>
</thead>

<tbody>
@forelse($products as $product)
<tr style="vertical-align:middle;">

<td>
@if($product->image)
    <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset($product->image) }}" width="60">
@endif
</td>

<td>
<a href="{{ url('product/'.\Illuminate\Support\Str::slug($product->manufacturer).'?part='.rawurlencode($product->product_key)) }}"
   style="color:#ea580c; font-weight:600; text-decoration:none;">
    {{ $product->product_key }}
</a>
</td>

<td style="max-width:300px;">
{{ \Illuminate\Support\Str::limit($product->description, 120) }}
</td>

<td style="font-weight:600;">
{{ $product->quantity }}
</td>

<td>
    <span style="color:#6b7280;">Contact</span>
</td>

<td>{{ $product->manufacturer }}</td>

<td>
<a href="{{ route('get_a_quote', [
    'part_number' => $product->product_key,
    'manufacturer' => $product->manufacturer
]) }}"
style="
    background:#f97316;
    color:#fff;
    padding:5px 10px;
    border-radius:5px;
    font-size:12px;
    text-decoration:none;
    display:inline-block;
">
Get Quote
</a>
</td>

</tr>
@empty
<tr>
<td colspan="7" class="text-center">
No products available
</td>
</tr>
@endforelse

</tbody>
</table>

<div class="mt-3 text-center">
{{ $products->onEachSide(1)->links() }}
</div>

</div>
</div>
</div>

</div>
</section>

@endsection