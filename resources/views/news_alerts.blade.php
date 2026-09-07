@extends('includes.front')

@section('seo')
<title>
    {{ isset($post) ? $post->title . ' | Simplytronix' : 'News & Alerts | Simplytronix' }}
</title>
<meta name="description"
      content="{{ isset($post) ? $post->excerpt : 'Latest news and updates from Simplytronix.' }}">
@stop

@section('content')

<main class="main__content_wrapper">

<section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.5); z-index:1;"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="row">
            <div class="col text-center text-white py-2">

                @if(isset($post))
                    <h1 class="breadcrumb__content--title mb-3 text-white">
                        {{ $post->title }}
                    </h1>
                @else
                    <h1 class="breadcrumb__content--title mb-3 text-white">
                        News & Insights
                    </h1>
                    <p class="lead text-white">Latest Updates From Simplytronix</p>
                @endif

            </div>
        </div>
    </div>
</section>

<section class="section--padding pt-3">
<div class="container">

<style>
.blog-wrapper { max-width: 780px; margin: auto; }
.blog-title { font-size: 24px; font-weight: 700; margin-bottom: 6px; line-height:1.3; }
.blog-date { color:#777; margin-bottom:15px; font-size:12px; }

.blog-image { 
    width: 100%;
    max-width: 680px;
    display: block;
    margin: 0 auto 16px auto;
    border-radius: 8px;
}

.blog-content { font-size:14px; line-height:1.6; color:#333; }

.blog-content img {
    width: 100%;
    max-width: 680px;
    height: auto;
    display: block;
    margin: 14px auto;
    border-radius: 6px;
}

.blog-content h2 { font-size:19px; margin-top:20px; margin-bottom:8px; }
.blog-content h3 { font-size:16px; margin-top:16px; margin-bottom:6px; }
.blog-content p { margin-bottom:10px; }
.blog-content ul { padding-left:18px; margin-bottom:12px; }
.blog-content li { margin-bottom:4px; }

.card img {
    width: 100%;
    height: auto;
    max-height: 170px;
    object-fit: contain;
    background: #f5f5f5;
}

.primary__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    line-height: 1;
}

.cta-box { margin-top:30px; padding:20px; background:#f8f8f8; border-radius:8px; text-align:center; }

.featured-img {
    width:100%;
    border-radius:10px;
    object-fit:cover;
    max-height:340px;
}

.featured-title {
    font-size:28px;
    font-weight:700;
    margin-top:10px;
    line-height:1.3;
}

.featured-excerpt {
    color:#666;
    font-size:14px;
    margin-top:10px;
}

.featured-meta {
    font-size:12px;
    color:#888;
    margin:10px 0;
}

.featured-tag {
    background:#ff5a2c;
    color:#fff;
    padding:4px 10px;
    font-size:12px;
    border-radius:4px;
}

@media (max-width: 768px) {
    .blog-wrapper { padding: 0 10px; }
    .blog-image,
    .blog-content img { max-width: 100%; }
}
</style>

@if(isset($post))

<div class="blog-wrapper">

    <div class="blog-title">
        {{ $post->title }}
    </div>

    <div class="blog-date">
        {{ $post->created_at ? $post->created_at->format('F d, Y') : '' }}
    </div>

    @if($post->featured_image)
    <img src="{{ url('public/uploads/blog/' . basename($post->featured_image)) }}"
         class="blog-image"
         loading="lazy"
         decoding="async"
         alt="{{ $post->title }}"
         onerror="this.style.display='none'">
@endif

    <div class="blog-content">
        {!! $post->content !!}
    </div>

    <div class="cta-box">
        <h4>Looking for Electronic Components?</h4>
        <p>Get fast quotes and reliable sourcing from Simplytronix.</p>
        <a href="{{ url('get-a-quote') }}" class="primary__btn">
            Request a Quote
        </a>
    </div>

</div>

@else

@if($posts->count())
@php $featured = $posts->first(); @endphp

<div class="row align-items-center mb-5">

    <div class="col-lg-6 mb-3">
        <img src="{{ url('public/uploads/blog/' . basename($featured->featured_image)) }}"
     class="featured-img"
     loading="lazy">
    </div>

    <div class="col-lg-6">

        

        <div class="featured-title">
            {{ $featured->title }}
        </div>

        <div class="featured-excerpt">
            {{ \Illuminate\Support\Str::limit($featured->excerpt, 150) }}
        </div>

        <div class="featured-meta">
            {{ $featured->created_at ? $featured->created_at->format('M d, Y') : '' }}
        </div>

        <a href="{{ url('blog/'.$featured->slug) }}" style="color:#ff5a2c; font-weight:600;">
            Read Post →
        </a>

    </div>

</div>
@endif

<div class="row">

@forelse($posts->skip(1) as $post)
<div class="col-lg-4 col-md-6 mb-3">
    <div class="card h-100 shadow-sm">

        <img src="{{ url('public/uploads/blog/' . basename($post->featured_image)) }}"
     loading="lazy"
     decoding="async"
     alt="{{ $post->title }}"
     onerror="this.style.display='none'">

        <div class="card-body p-3 d-flex flex-column">

            <h6 style="font-size:15px; font-weight:600;">
                {{ $post->title }}
            </h6>

            <p style="font-size:13px;">
                {{ \Illuminate\Support\Str::limit($post->excerpt, 100) }}
            </p>

            <div class="mt-auto text-center">
                <a href="{{ url('blog/'.$post->slug) }}" class="primary__btn" style="font-size:13px; padding:6px 12px;">
                    Read More
                </a>
            </div>

        </div>

    </div>
</div>
@empty

<div class="col text-center">
    <p>No blog posts available yet.</p>
</div>

@endforelse

</div>

<div class="mt-4 text-center">
    {{ $posts->links() }}
</div>

<div class="row justify-content-center mt-4">
    <div class="col text-center">
        <a href="{{ url('get-a-quote') }}" class="primary__btn">
            Request a Quote
        </a>
    </div>
</div>

@endif

</div>
</section>

</main>

@endsection