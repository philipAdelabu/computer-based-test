<?php

namespace Database\Seeders;

use App\Models\LocalGovernment;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    public function run(): void
    {
        $wards = [
            ['lg_code' => '01', 'code' => '01', 'name' => 'Ikeja Ward 1'],
            ['lg_code' => '01', 'code' => '02', 'name' => 'Ikeja Ward 2'],
            ['lg_code' => '02', 'code' => '01', 'name' => 'Agege Ward 1'],
            ['lg_code' => '02', 'code' => '02', 'name' => 'Agege Ward 2'],
            ['lg_code' => '03', 'code' => '01', 'name' => 'Alimosho Ward 1'],
            ['lg_code' => '03', 'code' => '02', 'name' => 'Alimosho Ward 2'],
        ];

        foreach ($wards as $ward) {
            $lg = LocalGovernment::where('code', $ward['lg_code'])->first();
            if ($lg) {
                Ward::create([
                    'local_government_id' => $lg->id,
                    'code' => $ward['code'],
                    'name' => $ward['name']
                ]);
            }
        }
    }
}