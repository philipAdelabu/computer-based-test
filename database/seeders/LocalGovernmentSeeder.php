<?php

namespace Database\Seeders;

use App\Models\LocalGovernment;
use Illuminate\Database\Seeder;

class LocalGovernmentSeeder extends Seeder
{
    public function run(): void
    {
        $lgs = [
            ['code' => '01', 'name' => 'Ikeja'],
            ['code' => '02', 'name' => 'Agege'],
            ['code' => '03', 'name' => 'Alimosho'],
            // Add more LGs as needed
        ];

        foreach ($lgs as $lg) {
            LocalGovernment::create($lg);
        }
    }
}