@extends('includes.front')

@section('seo')
<title>{{ $post->title }} | Simplytronix</title>
<meta name="description" content="{{ $post->excerpt }}">
@stop

@section('content')

<main class="main__content_wrapper">

<section class="py-4">

<style>
.blog-layout {
    max-width: 1100px;
    margin: auto;
}

.blog-main {
    max-width: 760px;
}

.blog-title {
    font-size:30px;
    font-weight:700;
    line-height:1.3;
    margin-bottom:8px;
}

.blog-meta {
    font-size:12px;
    color:#888;
    margin-bottom:18px;
}

.blog-image {
    width:100%;
    height:auto;
    border-radius:10px;
    margin-bottom:18px;
}

.blog-content {
    font-size:15px;
    line-height:1.8;
    color:#222;
}

.blog-content p:first-child {
    font-size:16px;
    font-weight:500;
}

.blog-content h2 {
    font-size:20px;
    margin-top:24px;
    margin-bottom:10px;
    font-weight:700;
    border-bottom:1px solid #eee;
    padding-bottom:6px;
}

.blog-content h3 {
    font-size:17px;
    margin-top:18px;
    margin-bottom:8px;
    font-weight:600;
}

.blog-content p { margin-bottom:12px; }

.blog-content ul {
    padding-left:18px;
    margin-bottom:14px;
}

.blog-content li { margin-bottom:5px; }

.blog-content img {
    max-width:100%;
    border-radius:6px;
    margin:16px 0;
}

.blog-content table {
    width:100%;
    border-collapse:collapse;
    margin:16px 0;
}

.blog-content table td,
.blog-content table th {
    border:1px solid #ddd;
    padding:6px;
    font-size:13px;
}

.blog-content blockquote {
    border-left:3px solid #ddd;
    padding-left:12px;
    color:#555;
    margin:16px 0;
    font-size:14px;
}

.sidebar-box {
    border:1px solid #eee;
    padding:15px;
    margin-bottom:20px;
}

.sidebar-title {
    font-size:14px;
    font-weight:600;
    margin-bottom:10px;
}

.sidebar-post {
    display:flex;
    gap:10px;
    margin-bottom:12px;
}

.sidebar-post img {
    width:70px;
    height:60px;
    object-fit:cover;
    border-radius:6px;
}

@media (max-width: 992px) {
    .blog-layout { padding: 0 10px; }
}
</style>

@php
    $plainText = strip_tags($post->content);
    $wordCount = str_word_count($plainText);
    $readTime = max(1, ceil($wordCount / 200));

    // Scope any CSS embedded inside the post's own content so it can NEVER
    // affect layout/width outside .blog-content, no matter what the post's
    // HTML declares (e.g. .container, body{}, * selectors, etc.)
    function sx_scope_css(string $css, string $scope): string
    {
        $result = '';
        $len = strlen($css);
        $i = 0;

        while ($i < $len) {
            $braceStart = strpos($css, '{', $i);

            if ($braceStart === false) {
                $result .= substr($css, $i);
                break;
            }

            $selectorPart = trim(substr($css, $i, $braceStart - $i));

            $depth = 1;
            $j = $braceStart + 1;
            while ($depth > 0 && $j < $len) {
                if ($css[$j] === '{') $depth++;
                elseif ($css[$j] === '}') $depth--;
                $j++;
            }
            $bodyPart = substr($css, $braceStart + 1, $j - $braceStart - 2);

            if ($selectorPart === '') {
                $result .= '{' . $bodyPart . '}';
            } elseif (preg_match('/^@(media|supports|document)/i', $selectorPart)) {
                $result .= $selectorPart . " {\n" . sx_scope_css($bodyPart, $scope) . "\n}\n";
            } elseif (preg_match('/^@(keyframes|font-face|page|import|charset)/i', $selectorPart)) {
                $result .= $selectorPart . " {" . $bodyPart . "}\n";
            } else {
                $selectors = array_map('trim', explode(',', $selectorPart));
                $scoped = array_map(function ($sel) use ($scope) {
                    if ($sel === '*') return $scope . ' *';
                    if (preg_match('/^(html|body)$/i', $sel)) return $scope;
                    if (strpos($sel, $scope) === 0) return $sel;
                    return $scope . ' ' . $sel;
                }, $selectors);
                $result .= implode(', ', $scoped) . " {" . $bodyPart . "}\n";
            }

            $i = $j;
        }

        return $result;
    }

    $rawContent = $post->content;
    $scopedStyles = '';

    if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $rawContent, $styleMatches)) {
        foreach ($styleMatches[1] as $styleBody) {
            $scopedStyles .= sx_scope_css($styleBody, '.blog-content') . "\n";
        }
        $rawContent = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $rawContent);
    }
@endphp

<div class="blog-layout">
    <div class="row">

        <div class="col-lg-8 blog-main">

            <div class="blog-title">
                {{ $post->title }}
            </div>

            <div class="blog-meta">
                {{ $post->created_at ? $post->created_at->format('M d, Y') : '' }} • {{ $readTime }} min{{ $readTime > 1 ? 's' : '' }} read
            </div>

            @if($post->featured_image)
                <img src="/public/uploads/blog/{{ basename($post->featured_image) }}"
                     class="blog-image"
                     alt="{{ $post->title }}">
            @endif

            <div class="blog-content">
                @if($scopedStyles)
                    <style>{!! $scopedStyles !!}</style>
                @endif
                {!! $rawContent !!}
            </div>

        </div>

        <div class="col-lg-4">

            <div class="sidebar-box">
                <div class="sidebar-title">Search Parts</div>

                <form action="{{ url('shop') }}" method="GET">
                    <input type="text"
                           name="key"
                           required
                           placeholder="Part # / Keyword"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </form>
            </div>

            <div class="sidebar-box">
                <div class="sidebar-title">Latest Posts</div>

                @foreach($latest ?? [] as $item)
                    <div class="sidebar-post">

                        <img src="/public/uploads/blog/{{ basename($item->featured_image) }}"
                             onerror="this.style.display='none'">

                        <div>
                            <a href="{{ url('blog/'.$item->slug) }}"
                               style="font-size:13px; line-height:1.3; font-weight:500; color:#222;">
                                {{ \Illuminate\Support\Str::limit($item->title, 60) }}
                            </a>

                            <div style="font-size:11px; color:#888;">
                                {{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </div>
</div>

</section>

</main>

@endsection
