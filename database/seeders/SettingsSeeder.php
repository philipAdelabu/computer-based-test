<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'school_name',
                'value' => 'CBT System',
                'type' => 'string',
                'group' => 'school',
                'label' => 'School Name',
                'description' => 'The name of your school or institution',
            ],
            [
                'key' => 'school_motto',
                'value' => 'Excellence in Education',
                'type' => 'string',
                'group' => 'school',
                'label' => 'School Motto',
                'description' => 'Your school\'s slogan or motto',
            ],
            [
                'key' => 'school_address',
                'value' => '',
                'type' => 'text',
                'group' => 'school',
                'label' => 'School Address',
                'description' => 'Physical address of the school',
            ],
            [
                'key' => 'school_phone',
                'value' => '',
                'type' => 'string',
                'group' => 'school',
                'label' => 'School Phone',
                'description' => 'Contact phone number',
            ],
            [
                'key' => 'school_email',
                'value' => '',
                'type' => 'string',
                'group' => 'school',
                'label' => 'School Email',
                'description' => 'Contact email address',
            ],
            [
                'key' => 'school_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'school',
                'label' => 'School Logo',
                'description' => 'Upload a logo (PNG, JPG, SVG). Recommended: 200x200px, transparent background',
            ],
            [
                'key' => 'landing_page_title',
                'value' => 'Welcome to Our CBT System',
                'type' => 'string',
                'group' => 'landing',
                'label' => 'Landing Page Title',
                'description' => 'Title displayed on the landing page',
            ],
            [
                'key' => 'landing_page_description',
                'value' => 'A modern computer-based testing platform for schools and institutions.',
                'type' => 'text',
                'group' => 'landing',
                'label' => 'Landing Page Description',
                'description' => 'Brief description shown on the landing page',
            ],
            [
                'key' => 'footer_text',
                'value' => '© ' . date('Y') . ' CBT System. All rights reserved.',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Footer Text',
                'description' => 'Text shown in the footer',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
