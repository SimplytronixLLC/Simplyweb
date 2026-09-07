<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $category = (object)[
            'name' => 'All Categories'
        ];

        $children = Category::whereNull('parent_id')->get();

        return view('categories.index', compact('category', 'children'));
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $children = Category::where('parent_id', $category->id)->get();

        return view('categories.index', compact('category', 'children'));
    }
}