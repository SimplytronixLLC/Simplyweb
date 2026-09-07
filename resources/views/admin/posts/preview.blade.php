<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PREVIEW: {{ $post->title }}</title>
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .preview-banner {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #b45309;
            color: #fff;
            text-align: center;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .blog-layout {
            max-width: 760px;
            margin: auto;
            padding: 30px 20px 60px;
        }

        .blog-title {
            font-size: 30px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .blog-content {
            font-size: 15px;
            line-height: 1.8;
            color: #222;
        }

        .blog-content p:first-child {
            font-size: 16px;
            font-weight: 500;
        }

        .blog-content h2 {
            font-size: 20px;
            margin-top: 24px;
            margin-bottom: 10px;
            font-weight: 700;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        .blog-content h3 {
            font-size: 17px;
            margin-top: 18px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .blog-content p { margin-bottom: 12px; }

        .blog-content ul {
            padding-left: 18px;
            margin-bottom: 14px;
        }

        .blog-content li { margin-bottom: 5px; }

        .blog-content img {
            max-width: 100%;
            border-radius: 6px;
            margin: 16px 0;
        }

        .blog-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        .blog-content table td,
        .blog-content table th {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 13px;
        }

        .blog-content blockquote {
            border-left: 3px solid #ddd;
            padding-left: 12px;
            color: #555;
            margin: 16px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="preview-banner">
        PREVIEW MODE &mdash; This post has not been published or saved.
    </div>

    <div class="blog-layout">

        @if(!empty($post->preview_image))
            <img src="{{ $post->preview_image }}"
                 style="width:100%;height:auto;border-radius:10px;margin-bottom:18px;"
                 alt="{{ $post->title }}">
        @endif

        <div class="blog-title">{{ $post->title }}</div>

        @if($post->excerpt)
            <div style="font-size:13px;color:#888;margin-bottom:20px;">
                {{ $post->excerpt }}
            </div>
        @endif

        <div class="blog-content">
            {!! $post->content !!}
        </div>
    </div>

</body>
</html>
