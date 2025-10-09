<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminBlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('author')->latest()->get();
        return view('admin.pages.blog.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.pages.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|max:4096',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique slug
            $slugBase = Str::slug($validated['title']);
            $slug = $slugBase;
            $counter = 1;
            while (Blog::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter++;
            }

            // Handle thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('blogs/thumbnails', 'public');
            }

            // Create blog
            $blog = Blog::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'category' => $validated['category'] ?? null,
                'thumbnail' => $thumbnailPath,
                'author_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.blogs.index')
                ->with('success', 'Blog post created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create blog: ' . $e->getMessage());
        };

    }


    public function show($id)
    {
        $blog = Blog::with(['details' => function ($query) {
            $query->orderBy('order');
        }, 'author'])->findOrFail($id);

        return view('admin.pages.blog.view', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blogs,slug,' . $id,
            'category' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|max:4096',
            'is_published' => 'nullable|boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('blogs/thumbnails', 'public');
        }

        $validated['is_published'] = $request->has('is_published');

        $blog->update($validated);

        return back()->with('success', 'Blog post updated successfully!');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->thumbnail) {
            Storage::disk('public')->delete($blog->thumbnail);
        }

        foreach ($blog->details as $detail) {
            if ($detail->type === 'image' && $detail->content) {
                Storage::disk('public')->delete($detail->content);
            }
        }

        $blog->delete();

        return redirect()->route('admin.pages.blog.index')
            ->with('success', 'Blog post deleted successfully!');
    }

    public function storeDetail(Request $request, $blogId)
    {
        $blog = Blog::findOrFail($blogId);

        $validatedBlocks = $request->validate([
            'blocks' => 'required|array|min:1',
            'blocks.*.type' => 'required|in:heading,paragraph,quote,list,image,code',
            'blocks.*.content' => 'required',
            'blocks.*.order' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($validatedBlocks, $blog, $request) {
            foreach ($validatedBlocks['blocks'] as $block) {
                $content = $block['content'];

                // Handle image uploads
                foreach ($request->blocks as $index => $block) {
                    if ($request->hasFile("blocks.$index.content")) {
                        $content = $request->file("blocks.$index.content")->store('blogs/content', 'public');
                    }
                }

                // Handle lists (convert to JSON)
                if ($block['type'] === 'list' && is_string($content)) {
                    $content = json_encode(array_filter(preg_split("/\r\n|\r|\n/", $content)));
                }

                BlogDetail::create([
                    'blog_id' => $blog->id,
                    'type' => $block['type'],
                    'content' => $content,
                    'order' => $block['order'] ?? ($blog->details()->max('order') + 1),
                ]);
            }
        });

        return back()->with('success', 'All content blocks added successfully!');
    }


    public function updateDetail(Request $request, $blogId, $detailId)
    {
        $detail = BlogDetail::where('blog_id', $blogId)->findOrFail($detailId);

        $validated = $request->validate([
            'content' => 'required',
            'extras' => 'nullable|array',
            'order' => 'nullable|integer|min:1',
        ]);

        $contentData = $validated['content'];

        // Handle image uploads
        if ($detail->type === 'image' && $request->hasFile('content')) {
            if (is_string($detail->content)) {
                Storage::disk('public')->delete($detail->content);
            }
            $contentData = $request->file('content')->store('blogs/content', 'public');
        }

        // Handle lists
        if ($detail->type === 'list' && is_array($contentData)) {
            $contentData = json_encode(array_filter($contentData));
        }

        $detail->update([
            'content' => $contentData,
            'extras' => $validated['extras'] ?? $detail->extras,
            'order' => $validated['order'] ?? $detail->order,
        ]);

        return back()->with('success', 'Content updated successfully!');
    }

    public function destroyDetail($blogId, $detailId)
    {
        $detail = BlogDetail::where('blog_id', $blogId)->findOrFail($detailId);

        if ($detail->type === 'image' && is_string($detail->content)) {
            Storage::disk('public')->delete($detail->content);
        }

        $detail->delete();

        return back()->with('success', 'Content deleted successfully!');
    }
}
