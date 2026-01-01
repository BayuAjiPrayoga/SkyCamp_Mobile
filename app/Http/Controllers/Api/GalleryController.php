<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * List approved gallery photos
     */
    public function index(Request $request)
    {
        $galleries = Gallery::where('status', 'approved')
            ->with('user:id,name,avatar')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $galleries,
        ]);
    }

    /**
     * Upload new photo to gallery
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Gallery Upload Request Start', [
            'user_id' => $request->user()?->id,
            'has_file' => $request->hasFile('image'),
            'file_valid' => $request->file('image')?->isValid(),
            'file_size' => $request->file('image')?->getSize(),
            'content_length' => $request->header('Content-Length'),
        ]);

        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
            'caption' => 'nullable|string|max:500',
        ]);

        $path = $request->file('image')->store('galleries', 'public');

        \Illuminate\Support\Facades\Log::info('Gallery Upload Success', ['path' => $path]);

        $gallery = Gallery::create([
            'user_id' => $request->user()->id,
            'image_path' => $path,
            'caption' => $request->caption,
            'status' => 'pending', // Needs admin approval
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload. Menunggu moderasi admin.',
            'data' => $gallery,
        ], 201);
    }
}
