<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FuncController;
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
            'theme_primary' => 'nullable|string|max:20',
            'theme_secondary' => 'nullable|string|max:20',
            'theme_report_header' => 'nullable|string|max:20',
            'theme_report_text' => 'nullable|string|max:20',
            'ca_weight' => 'nullable|integer|min:1|max:99',
            'exam_weight' => 'nullable|integer|min:1|max:99',
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

              foreach (['theme_primary', 'theme_secondary', 'theme_report_header', 'theme_report_text'] as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
           }

            // Handle logo upload
            if ($request->hasFile('school_logo')) {
                $logoPath = $this->uploadImage($request->file('school_logo'));
                
                // Delete old logo
                $oldLogo = Setting::get('school_logo');
                if ($oldLogo) {
                    $this->deleteImage($oldLogo);
                }
                
                Setting::set('school_logo', $logoPath, 'image');
            }


            if ($request->hasFile('principal_signature')) {
                $imagePath = $this->uploadImage($request->file('principal_signature'), 'signatures');
                Setting::set('principal_signature', $imagePath, 'image');
            }
            if ($request->hasFile('school_stamp')) {
                $imagePath = $this->uploadImage($request->file('school_stamp'), 'signatures');
                Setting::set('school_stamp', $imagePath, 'image');
            }

            // Handle logo removal
            if ($request->has('remove_logo') && $request->remove_logo == '1') {
                $oldLogo = Setting::get('school_logo');
                if ($oldLogo) {
                    $this->deleteImage($oldLogo);
                }
                Setting::set('school_logo', null, 'image');
            }

                if ($request->has('ca_weight')) {
                    Setting::set('ca_weight', $request->input('ca_weight'));
                }
                
                if ($request->has('exam_weight')) {
                    Setting::set('exam_weight', $request->input('exam_weight'));
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
    private function uploadImage($file, $folder='settings')
    { 
        return FuncController::uploadImage($file, $folder);
    }

      private function deleteImage($imagePath){
         FuncController::deleteImage($imagePath);
    }


}