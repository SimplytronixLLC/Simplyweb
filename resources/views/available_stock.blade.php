@extends('includes.front')

@section('seo')
<title>Available Stock</title>
<meta name="description" content="Available stock of electronic components">
@endsection

@section('content')

<section style="background:#f8fafc; padding:30px 0;">
<div class="container-fluid">

<div style="background:#ffffff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">

<div style="padding:16px 20px; border-bottom:1px solid #e5e7eb;">
<h4 style="margin:0; font-size:18px; font-weight:600;">Available Stock</h4>
</div>

<div style="padding:18px;">

<div class="table-responsive">

<form method="GET" action="{{ url('/available-stock') }}" id="filterForm">

<table class="table" style="font-size:14px; table-layout:fixed; width:100%;">

<thead style="background:#f9fafb;">

<tr>
<th style="width:90px;">Image</th>
<th style="width:180px;">Part Number</th>
<th style="width:260px;">Description</th>
<th style="width:100px;">Quantity</th>
<th style="width:110px;">Price</th>
<th style="width:170px;">Category</th>
<th style="width:170px;">Sub-Category</th>
<th style="width:180px;">Manufacturer</th>
<th style="width:120px;">Quote</th>
</tr>

<tr style="background:#ffffff;">
<th colspan="5"></th>

<th>
<select name="level1" id="level1" class="form-select form-select-sm" style="font-size:13px; width:160px;">
<option value="">All</option>
@foreach($level1Categories as $cat)
<option value="{{ $cat->id }}" {{ request('level1') == $cat->id ? 'selected' : '' }}>
{{ $cat->name }}
</option>
@endforeach
</select>
</th>

<th>
<select name="level2" id="level2" class="form-select form-select-sm" style="font-size:13px; width:160px;">
<option value="">All</option>
</select>
</th>

<th>
<select name="manufacturer" id="manufacturer" class="form-select form-select-sm" style="font-size:13px; width:170px;">
<option value="">All</option>
@foreach($manufacturers as $mfg)
<option value="{{ $mfg }}" {{ request('manufacturer') == $mfg ? 'selected' : '' }}>
{{ $mfg }}
</option>
@endforeach
</select>
</th>

<th class="text-center">
<a href="{{ url('/available-stock') }}" style="font-size:12px; color:#f97316; text-decoration:none; font-weight:500;">
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
<img src="{{ str_starts_with($product->image,'http') ? $product->image : asset($product->image) }}" width="70">
@endif
</td>

<td>
@php
$manufacturerSlug = \Illuminate\Support\Str::slug($product->manufacturer);

$safePart = rawurlencode(
    str_replace(['/', '#'], ['__', '--'], $product->product_key)
);
@endphp

<a href="{{ url('product/'.$manufacturerSlug.'/'.$safePart) }}"
style="color:#ea580c; font-weight:600; text-decoration:none;">
{{ $product->product_key }}
</a>
</td>

<td style="max-width:260px; position:relative;">
@php
$desc = $product->description ?? '';
$isLong = strlen($desc) > 80;
@endphp

@if($isLong)
<div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;"
onmouseenter="this.nextElementSibling.style.display='block'; this.style.visibility='hidden';"
onmouseleave="this.nextElementSibling.style.display='none'; this.style.visibility='visible';">
{{ $desc }}
</div>

<div style="display:none;position:absolute;left:0;top:0;width:100%;background:#ffffff;padding:10px;font-size:13px;line-height:1.5;border-radius:6px;box-shadow:0 4px 14px rgba(0,0,0,0.12);border:1px solid #e5e7eb;z-index:10;"
onmouseenter="this.style.display='block'; this.previousElementSibling.style.visibility='hidden';"
onmouseleave="this.style.display='none'; this.previousElementSibling.style.visibility='visible';">
{{ $desc }}
</div>
@else
<div>{{ $desc }}</div>
@endif
</td>

<td style="font-weight:600;">{{ $product->quantity }}</td>

<td>
<span style="color:#6b7280;">Contact</span>
</td>

@php
$cat = $product->categoryRelation ?? null;
@endphp

<td>
@if($cat)
    @if($cat->level == 3)
        {{ optional(optional($cat->parent)->parent)->name ?? 'N/A' }}
    @elseif($cat->level == 2)
        {{ optional($cat->parent)->name ?? 'N/A' }}
    @else
        {{ $cat->name }}
    @endif
@else
    N/A
@endif
</td>

<td>
@if($cat)
    @if($cat->level == 3)
        {{ $cat->name }}
    @elseif($cat->level == 2)
        {{ $cat->name }}
    @else
        N/A
    @endif
@else
    N/A
@endif
</td>

<td>{{ $product->manufacturer }}</td>

<td style="white-space:nowrap; text-align:center;">
<a href="{{ url('/get-a-quote') }}?name={{ urlencode($product->product_key) }}&details={{ urlencode($product->description) }}"
style="background:#f97316;color:#fff;padding:6px 14px;border-radius:5px;font-size:12px;text-decoration:none;display:inline-block;min-width:90px;text-align:center;">
Get Quote
</a>
</td>

</tr>

@empty

<tr>
<td colspan="9" class="text-center">No products available</td>
</tr>

@endforelse

</tbody>

</table>

</form>

<div class="mt-3 text-center">
{{ $products->onEachSide(1)->links() }}
</div>
<style>
.pagination-wrapper .pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.pagination-wrapper .pagination li:first-child {
    margin-right: auto; /* pushes numbers center */
}

.pagination-wrapper .pagination li:last-child {
    margin-left: auto; /* pushes next to right */
}

/* Center page numbers */
.pagination-wrapper .pagination li:not(:first-child):not(:last-child) {
    margin: 0 4px;
}
</style>
</div>
</div>
</div>

</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

const level1 = document.getElementById('level1');
const level2 = document.getElementById('level2');
const manufacturer = document.getElementById('manufacturer');
const form = document.getElementById('filterForm');
const selectedLevel2 = "{{ request('level2') }}";

function loadLevel2(parentId, selected = null) {

    level2.innerHTML = '<option value="">All</option>';
    if (!parentId) return;

    fetch("{{ url('/get-level2') }}/" + parentId)
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data)) {
            data.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                if (selected && selected == item.id) option.selected = true;
                level2.appendChild(option);
            });
        }
    });
}

if (level1.value) {
    loadLevel2(level1.value, selectedLevel2);
}

level1.addEventListener('change', function () {
    level2.value = '';
    loadLevel2(this.value);
    form.submit();
});

level2.addEventListener('change', function () {
    form.submit();
});

manufacturer.addEventListener('change', function () {
    form.submit();
});

});
</script>

@endsection