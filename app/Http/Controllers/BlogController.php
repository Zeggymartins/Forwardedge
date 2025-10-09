<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::with(['author', 'details'])->where('is_published', true)->latest()->paginate(6);
        if ($request->ajax()) {
            return response()->json([
                'blogs' => view('user.pages.blog', compact('blogs'))->render(),
                'pagination' => (string) $blogs->links('pagination::bootstrap-4'),
            ]);
        }

        // Blog categories with counts
        $categories = Blog::select('category', DB::raw('COUNT(*) as count'))->where('is_published', true)
            ->groupBy('category')
            ->pluck('count', 'category');

        $latestPosts = Blog::latest()->where('is_published', true)->take(3)->get();

        return view('user.pages.blog', compact('blogs', 'categories', 'latestPosts'));
    }

    public function show($slug)
    {
        $blog = Blog::with(['author', 'details'])->where('slug', $slug)->firstOrFail();

        // Related posts: same category, excluding current blog
        $relatedBlogs = Blog::where('category', $blog->category)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get();

        if ($blog->details->isEmpty()) {
            return redirect()->route('blog')->with('error', 'No details available for this post yet.');
        }
        // Blog categories with counts
        $categories = Blog::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        return view('user.pages.blog_details', compact('blog', 'relatedBlogs', 'categories'));
    }
}
