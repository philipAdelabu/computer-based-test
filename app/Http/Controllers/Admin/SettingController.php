<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('label')->get();
        
        // Group settings
        $groupedSettings = $settings->groupBy('group');
        
        return view('admin.settings.index', compact('groupedSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_motto' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'landing_page_title' => 'nullable|string|max:255',
            'landing_page_description' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:255',
        ]);

        try {
            // Handle text settings
            foreach (['school_name', 'school_motto', 'school_address', 'school_phone', 
                      'school_email', 'landing_page_title', 'landing_page_description', 
                      'footer_text'] as $key) {
                if ($request->has($key)) {
                    Setting::set($key, $request->input($key));
                }
            }

            // Handle logo upload
            if ($request->hasFile('school_logo')) {
                $logoPath = $this->uploadLogo($request->file('school_logo'));
                
                // Delete old logo
                $oldLogo = Setting::get('school_logo');
                if ($oldLogo) {
                    $oldPath = public_path($oldLogo);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                
                Setting::set('school_logo', $logoPath, 'image');
            }

            // Handle logo removal
            if ($request->has('remove_logo') && $request->remove_logo == '1') {
                $oldLogo = Setting::get('school_logo');
                if ($oldLogo) {
                    $oldPath = public_path($oldLogo);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                Setting::set('school_logo', null, 'image');
            }

            Setting::clearCache();

            return redirect()->route('admin.settings')
                ->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Upload logo to public/uploads/settings folder
     */
    private function uploadLogo($file)
    {
        $uploadPath = public_path('uploads/settings');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        return 'uploads/settings/' . $filename;
    }
}