@extends('includes.front')

@section('content')

<div class="container py-4">

    <h2 class="mb-2">{{ $manufacturer }}</h2>
    <p class="text-muted">{{ $total }} Products Available</p>

    <div class="row">
        @foreach($products as $product)
        <div class="col-md-3 col-6 mb-4">
            <div class="card h-100 p-2">

                <img src="{{ $product->image }}" class="img-fluid mb-2" onerror="this.src='/images/no-image.png'">

                <h6>{{ $product->name }}</h6>

                <small class="text-muted">
                    {{ $product->category }}
                </small>

                <div class="mt-2">
                    <strong style="color:#6b7280;">Contact for Pricing</strong>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

</div>

@endsection