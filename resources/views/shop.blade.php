@extends('includes.front')

@section('seo')

<title>{{$settings->meta_title}}</title> 
<meta name="title" content="{{$settings->meta_title}}">
<meta name="keywords" content="{{$settings->meta_keyword}}">
<meta name="description" content="{{$settings->meta_description}}">
<meta name="language" content="en">

<meta property="og:url" content="{{url('/')}}">
<meta property="og:site_name" content="{{$settings->meta_title}}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{$settings->meta_title}}">
<meta property="og:description" content="{{$settings->meta_description}}">
<meta property="og:image" content="{{url('public')}}/{{$settings->logo}}">

<meta name="twitter:title" content="{{$settings->meta_title}}">
<meta name="twitter:site" content="@yourTwitterHandle">
<meta name="twitter:description" content="{{$settings->meta_description}}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{url('public')}}/{{$settings->logo}}"> 

@stop


@section('content')

<section style="background:#f8fafc; padding:30px 0;">
<div class="container-fluid">

<div style="background:#ffffff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.05);margin-bottom:20px;">
<div style="padding:14px 20px; border-bottom:1px solid #e5e7eb;">
<h4 style="margin:0; font-size:16px; font-weight:600; color:#374151;">
Results for "{{ request('key') }}"
</h4>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 18px 14px 18px;font-size:14px;">
<div><strong>Results:</strong> {{ $products->total() }}</div>

<div>
<form method="GET" action="{{ url()->current() }}" style="display:inline;">
@foreach(request()->except('per_page') as $key => $value)
<input type="hidden" name="{{ $key }}" value="{{ $value }}">
@endforeach

<select name="per_page" onchange="this.form.submit()" style="border:1px solid #e5e7eb;border-radius:4px;padding:2px 6px;font-size:13px;margin-left:6px;">
<option value="10" {{ request('per_page',10) == 10 ? 'selected' : '' }}>10</option>
<option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
<option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
</select>
</form>
</div>
</div>

<div style="background:#ffffff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
<div style="padding:18px;">
<div class="table-responsive">

<table class="table" style="font-size:14px;">
<thead style="background:#f9fafb;">
<tr>
<th style="width:120px;">Image</th>
<th>Name</th>
<th style="width:110px;">Quantity</th>
<th style="width:110px;">Price</th>
<th>Category</th>
<th>Manufacturer</th>
<th style="width:120px;">Quote</th>
</tr>
</thead>

<tbody id="product-list">

@foreach ($products as $product)

<tr>
<td style="text-align:center;">
@if (!empty($product['PhotoUrl']))
@php $img = $product['PhotoUrl']; @endphp
<img src="{{ str_starts_with($img,'http') ? $img : asset($img) }}" height="90" width="90">
@else
<div style="height:90px;width:90px;display:flex;align-items:center;justify-content:center;margin:auto;font-size:12px;color:#6b7280;">
No Image
</div>
@endif
</td>

<td>
@php
$partNumber = $product['ProductVariations'][0]['DigiKeyProductNumber'];
$manufacturerName = $product['Manufacturer']['Name'] ?? '';
$manufacturerSlug = \Illuminate\Support\Str::slug($manufacturerName);

$safePart = rawurlencode(
    str_replace(['/', '#'], ['__', '--'], $partNumber)
);
@endphp

<a href="{{ url('product/'.$manufacturerSlug.'/'.$safePart) }}" style="color:#ea580c;font-weight:600;text-decoration:none;">
{{ $partNumber }}
</a>

<div style="font-size:13px;color:#6b7280;margin-top:4px;">
{{ $product['Description']['ProductDescription'] }}
</div>
</td>

<td>{{ $product['QuantityAvailable'] }}</td>

<td>
<span style="color:#6b7280;">Contact</span>
</td>

<td>{{ $product['Category']['Name'] }}</td>
<td>{{ $product['Manufacturer']['Name'] }}</td>

<td>
<a href="{{url('/get-a-quote')}}?name={{ $partNumber }}" 
style="background:#f97316;color:#fff;padding:6px 10px;border-radius:5px;font-size:12px;text-decoration:none;">
Get Quote
</a>
</td>

</tr>

@endforeach

</tbody>
</table>

<div id="loading" style="text-align:center;padding:20px;display:none;">Loading...</div>

</div>
</div>
</div>

</div>
</div>
</section>

<script>
let nextPage = "{{ $products->nextPageUrl() }}";
let loading = false;

function createRow(item) {
    let part = item.ProductVariations[0].DigiKeyProductNumber;
    let manufacturer = item.Manufacturer.Name || '';

    let slug = manufacturer.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    if (!slug) slug = 'unknown';

    let safePart = part.replaceAll('/', '__').replaceAll('#', '--');
    let url = `/product/${slug}/${safePart}`;

    return `
    <tr>
        <td style="text-align:center;">
            ${item.PhotoUrl ? `<img src="${item.PhotoUrl}" height="90" width="90">` : 'No Image'}
        </td>
        <td>
            <a href="${url}" style="color:#ea580c;font-weight:600;text-decoration:none;">
                ${part}
            </a><br>
            <small>${manufacturer}</small><br>
            <small>${item.Description.ProductDescription}</small>
        </td>
        <td>${item.QuantityAvailable}</td>
        <td>Contact</td>
        <td>${item.Category.Name}</td>
        <td>${manufacturer}</td>
        <td>
            <a href="/get-a-quote?name=${part}" 
            style="background:#f97316;color:#fff;padding:6px 10px;border-radius:5px;font-size:12px;text-decoration:none;">
            Get Quote
            </a>
        </td>
    </tr>`;
}

window.addEventListener('scroll', function () {

    if (loading || !nextPage) return;

    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {

        loading = true;
        document.getElementById('loading').style.display = 'block';

        fetch(nextPage, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {

            nextPage = data.next_page;
            loading = false;
            document.getElementById('loading').style.display = 'none';

            let container = document.getElementById('product-list');

            data.data.forEach(item => {
                container.insertAdjacentHTML('beforeend', createRow(item));
            });

        });
    }
});

setInterval(() => {

    fetch(nextPage, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
    .then(res => res.json())
    .then(data => {

        if (!data.data) return;

        let container = document.getElementById('product-list');
        let existing = new Set();

        document.querySelectorAll('#product-list tr').forEach(tr => {
            existing.add(tr.innerText);
        });

        data.data.forEach(item => {

            let key = item.ProductVariations[0].DigiKeyProductNumber;

            if ([...existing].some(e => e.includes(key))) return;

            container.insertAdjacentHTML('beforeend', createRow(item));
        });

    });

}, 5000);
</script>

@endsection