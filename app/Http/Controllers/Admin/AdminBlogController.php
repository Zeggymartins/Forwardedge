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
use Illuminate\Validation\ValidationException;

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
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
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
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
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
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
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

            if ($detail->type === 'video' && $detail->videoIsLocal()) {
                Storage::disk('public')->delete($detail->rawContent());
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
            'blocks.*.type' => 'required|in:heading,paragraph,quote,list,image,video',
            'blocks.*.content' => 'nullable',
            'blocks.*.order' => 'nullable|integer|min:1',
            'blocks.*.video_source' => 'nullable|in:upload,url',
            'blocks.*.video_url' => 'nullable|url',
            'blocks.*.video_orientation' => 'nullable|in:landscape,portrait',
            'blocks.*.video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv,mkv,mpg,mpeg,webm|max:102400',
        ]);

        DB::transaction(function () use ($validatedBlocks, $blog, $request) {
            $nextOrder = (int) ($blog->details()->max('order') ?? 0);

            foreach ($validatedBlocks['blocks'] as $index => $block) {
                $content = $block['content'] ?? null;
                $type = $block['type'];
                $extras = [];

                if ($type === 'image') {
                    if (!$request->hasFile("blocks.$index.content")) {
                        throw ValidationException::withMessages([
                            "blocks.$index.content" => 'Please upload an image.',
                        ]);
                    }
                    $content = $request->file("blocks.$index.content")->store('blogs/content', 'public');
                } elseif ($type === 'list') {
                    $items = $this->normalizeListItems($block['content'] ?? []);
                    if (empty($items)) {
                        throw ValidationException::withMessages([
                            "blocks.$index.content" => 'Add at least one list item.',
                        ]);
                    }
                    $content = json_encode($items);
                } elseif ($type === 'video') {
                    $source = $block['video_source'] ?? ($request->hasFile("blocks.$index.video_file") ? 'upload' : 'url');
                    $source = in_array($source, ['upload', 'url'], true) ? $source : 'upload';
                    $orientation = in_array($block['video_orientation'] ?? '', ['portrait', 'landscape'], true)
                        ? $block['video_orientation']
                        : 'landscape';

                    $extras = [
                        'orientation' => $orientation,
                        'source' => $source,
                    ];

                    if ($source === 'upload') {
                        if (!$request->hasFile("blocks.$index.video_file")) {
                            throw ValidationException::withMessages([
                                "blocks.$index.video_file" => 'Upload a video file.',
                            ]);
                        }
                        $file = $request->file("blocks.$index.video_file");
                        $content = $file->store('blogs/videos', 'public');
                        $extras['mime'] = $file->getMimeType();
                    } else {
                        $videoUrl = trim((string) ($block['video_url'] ?? ''));
                        if (blank($videoUrl)) {
                            throw ValidationException::withMessages([
                                "blocks.$index.video_url" => 'Provide a valid video URL.',
                            ]);
                        }
                        $content = $videoUrl;
                    }
                } else {
                    if (!filled($content)) {
                        throw ValidationException::withMessages([
                            "blocks.$index.content" => 'This block requires content.',
                        ]);
                    }
                }

                $orderValue = isset($block['order']) ? (int) $block['order'] : ++$nextOrder;
                if (isset($block['order'])) {
                    $nextOrder = max($nextOrder, $orderValue);
                }

                $blog->details()->create([
                    'type'   => $type,
                    'content'=> $content,
                    'order'  => $orderValue,
                    'extras' => !empty($extras) ? $extras : null,
                ]);
            }
        });

        return back()->with('success', 'All content blocks added successfully!');
    }


    public function updateDetail(Request $request, $blogId, $detailId)
    {
        $detail = BlogDetail::where('blog_id', $blogId)->findOrFail($detailId);

        $rules = [
            'extras' => 'nullable|array',
            'order' => 'nullable|integer|min:1',
        ];

        if ($detail->type === 'list') {
            $rules['content'] = 'required|array|min:1';
        } elseif ($detail->type === 'image') {
            $rules['content'] = 'nullable';
        } elseif ($detail->type === 'video') {
            $rules['content'] = 'nullable';
            $rules['video_source'] = 'nullable|in:upload,url';
            $rules['video_url'] = 'nullable|url';
            $rules['video_file'] = 'nullable|file|mimes:mp4,mov,avi,wmv,mkv,mpg,mpeg,webm|max:102400';
            $rules['orientation'] = 'nullable|in:landscape,portrait';
        } else {
            $rules['content'] = 'required';
        }

        $validated = $request->validate($rules);

        $contentData = $validated['content'] ?? $detail->content;
        $extrasPayload = $validated['extras'] ?? $detail->extras ?? [];

        // Handle image uploads
        if ($detail->type === 'image' && $request->hasFile('content')) {
            if (is_string($detail->content)) {
                Storage::disk('public')->delete($detail->content);
            }
            $contentData = $request->file('content')->store('blogs/content', 'public');
        }

        // Handle lists
        if ($detail->type === 'list') {
            $contentData = json_encode($this->normalizeListItems($contentData));
        }
        if ($detail->type === 'video') {
            $source = $validated['video_source'] ?? ($detail->extras['source'] ?? ($detail->videoIsLocal() ? 'upload' : 'url'));
            $source = in_array($source, ['upload', 'url'], true) ? $source : 'upload';
            $orientation = $validated['orientation'] ?? ($detail->extras['orientation'] ?? 'landscape');
            $extrasPayload['orientation'] = $orientation;
            $extrasPayload['source'] = $source;

            if ($source === 'upload') {
                if ($request->hasFile('video_file')) {
                    if ($detail->videoIsLocal()) {
                        Storage::disk('public')->delete($detail->rawContent());
                    }
                    $file = $request->file('video_file');
                    $contentData = $file->store('blogs/videos', 'public');
                    $extrasPayload['mime'] = $file->getMimeType();
                } elseif (!$detail->videoIsLocal()) {
                    throw ValidationException::withMessages([
                        'video_file' => 'Upload a video file or switch to URL.',
                    ]);
                } else {
                    $contentData = $detail->rawContent();
                }
            } else {
                $url = trim((string) ($validated['video_url'] ?? ''));
                if (blank($url) && $detail->videoIsExternal()) {
                    $url = $detail->rawContent();
                }

                if (blank($url)) {
                    throw ValidationException::withMessages([
                        'video_url' => 'Provide a valid video URL.',
                    ]);
                }

                if ($detail->videoIsLocal()) {
                    Storage::disk('public')->delete($detail->rawContent());
                }

                $contentData = $url;
            }
        }

        $detail->update([
            'content' => $contentData,
            'extras' => $extrasPayload,
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

        if ($detail->type === 'video' && $detail->videoIsLocal()) {
            Storage::disk('public')->delete($detail->rawContent());
        }

        $detail->delete();

        return back()->with('success', 'Content deleted successfully!');
    }

    private function normalizeListItems(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        if (is_string($value)) {
            return collect(preg_split("/\r\n|\r|\n/", $value))
                ->map(fn($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        return [];
    }
}
