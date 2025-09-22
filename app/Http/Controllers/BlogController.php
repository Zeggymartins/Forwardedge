<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['author', 'details'])->latest()->paginate(6);
        return view('user.pages.blog', compact('blogs'));
    }

    public function show($slug)
    {
        $blog = Blog::with(['author', 'details'])->where('slug', $slug)->firstOrFail();
        return view('user.pages.blog_details', compact('blog'));
    }
}
