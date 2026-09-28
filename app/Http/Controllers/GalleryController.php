<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::latest();
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $photos     = $query->paginate(20);
        $categories = Gallery::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.pages.gallery', compact('photos', 'categories'));
    }

    public function getPhotos(Request $request)
    {
        $query = Gallery::latest();
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $photos     = $query->paginate(12);
        $categories = Gallery::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('user.pages.gallery', compact('photos', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'images'   => 'required|array|max:20',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $uploaded = 0;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');

                Gallery::create([
                    'title'    => $request->title,
                    'category' => $request->filled('category') ? $request->category : null,
                    'image'    => $path,
                ]);

                $uploaded++;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'uploaded' => $uploaded,
                'message' => "{$uploaded} photo(s) uploaded successfully.",
            ]);
        }

        return redirect()->back()->with('success', 'Photos uploaded successfully!');
    }


    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'    => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $gallery->title    = $request->title;
        $gallery->category = $request->filled('category') ? $request->category : null;

        if ($request->hasFile('image')) {
            if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
                Storage::disk('public')->delete($gallery->image);
            }

            $gallery->image = $request->file('image')->store('gallery', 'public');
        }

        $gallery->save();

        return redirect()->back()->with('success', 'Photo updated successfully!');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return redirect()->back()->with('success', 'Photo deleted successfully!');
    }
}
