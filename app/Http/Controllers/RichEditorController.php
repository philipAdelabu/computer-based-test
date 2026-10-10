<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RichEditorController extends Controller
{
    /**
     * Handle image uploads from TinyMCE
     */
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        // Validate
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Save to public/uploads/editor
        $uploadPath = public_path('uploads/editor');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = 'editor_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        $url = asset('uploads/editor/' . $filename);

        // TinyMCE expects { location: "..." }
        return response()->json([
            'location' => $url,
        ]);
    }
}