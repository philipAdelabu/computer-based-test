<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FuncController extends Controller
{
    //


     // ============ HELPER METHODS ============

        /**
         * Upload a photo to public/uploads/{folder}
         */
        public static function uploadPhoto($file, $folder = 'users')
        {
            $uploadPath = public_path('uploads/' . $folder);
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            return 'uploads/' . $folder . '/' . $filename;
        }

        /**
         * Delete a photo from wherever it lives
         */
        public static function deletePhoto($photoPath)
        {
            if (!$photoPath) return;

            $publicPath = public_path($photoPath);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }

        }

          public static function deleteImage($imagePath)
        {
            if (!$imagePath) return;

            $publicPath = public_path($imagePath);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
            
        }

             /**
     * Upload image to public/uploads/questions folder
     */
    public static function uploadImage($file, $folder = 'imageUploads')
    {
        // Ensure directory exists
        $uploadPath = public_path('uploads/' . $folder);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Move file to public folder
        $file->move($uploadPath, $filename);
        
        // Return relative path for storage in DB
        return 'uploads/' . $folder . '/' . $filename;
    }



}
