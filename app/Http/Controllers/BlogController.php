<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_published', 1)
            ->latest()
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', 1)
            ->firstOrFail();

        $latest = Post::where('is_published', 1)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(5)
            ->get();

        return view('blog.show', compact('post', 'latest'));
    }
}