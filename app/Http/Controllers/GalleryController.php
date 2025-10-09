<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = Gallery::latest()->get();
        return view('admin.pages.gallery', compact('photos'));
    }
    public function getPhotos()
    {
        $photos = Gallery::latest()->get();
        return view('user.pages.gallery', compact('photos'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',   // single shared title
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');

                Gallery::create([
                    'title' => $request->title, // same title for all images
                    'image' => $path
                ]);
            }
        }

        return redirect()->back()->with('success', 'Photos uploaded successfully!');
    }


    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $gallery->title = $request->title;

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
