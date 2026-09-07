@extends('includes.front')

@section('content')

<div class="container-fluid py-4">

    <div class="row justify-content-center">

        <div class="col-xl-10">

            <div class="row">

                {{-- LEFT SECTION --}}
                <div class="col-lg-9">

                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Top Manufacturers</h2>
                    </div>

                    <div class="manufacturer-grid">

                        @foreach($topManufacturers as $m)
                        <a href="{{ url('/available-stock') }}?manufacturer={{ urlencode($m->manufacturer) }}"
                           class="manufacturer-card"
                           title="View {{ $m->manufacturer }} products">

                            <div class="name">
                                {{ $m->manufacturer }}
                            </div>

                            <div class="count">
                                {{ $m->total_products }} parts
                            </div>

                        </a>
                        @endforeach

                    </div>

                </div>

                {{-- RIGHT SIDEBAR --}}
                <div class="col-lg-3">

                    <div class="sidebar-box">

                        <h6 class="fw-bold mb-3">Find Manufacturer</h6>

                        <input type="text" id="searchInput"
                               class="form-control form-control-sm mb-3"
                               placeholder="Search manufacturer...">

                        <div class="alphabet-grid">
                            @foreach(range('A','Z') as $char)
                                <span onclick="goToLetter('{{ $char }}')">{{ $char }}</span>
                            @endforeach
                            <span class="active" onclick="resetFilter()">All</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<style>

/* GRID */
.manufacturer-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
}

/* CARD */
.manufacturer-card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 12px 10px;
    text-align: center;
    text-decoration: none;
    color: #111;
    transition: 0.2s;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* HOVER */
.manufacturer-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
}

/* TEXT */
.name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
    min-height: 34px;
}

.count {
    font-size: 11px;
    color: #777;
}

/* SIDEBAR */
.sidebar-box {
    background: #fff;
    padding: 14px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    position: sticky;
    top: 20px;
}

/* ALPHABET */
.alphabet-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
}

.alphabet-grid span {
    text-align: center;
    font-size: 11px;
    padding: 6px 0;
    background: #f1f1f1;
    border-radius: 6px;
    cursor: pointer;
}

.alphabet-grid span:hover,
.alphabet-grid span.active {
    background: #000;
    color: #fff;
}

/* RESPONSIVE */
@media(max-width: 1200px){
    .manufacturer-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media(max-width: 768px){
    .manufacturer-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            let value = this.value.trim();
            if (value !== '') {
                window.location.href = "{{ url('/available-stock') }}?manufacturer=" + encodeURIComponent(value);
            }
        }
    });

});

function goToLetter(letter) {
    window.location.href = "{{ url('/available-stock') }}?manufacturer=" + letter + "*";
}

function resetFilter() {
    window.location.href = "{{ url('/available-stock') }}";
}

</script>

@endsection