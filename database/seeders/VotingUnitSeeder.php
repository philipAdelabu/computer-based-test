<?php

namespace Database\Seeders;

use App\Models\VotingUnit;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class VotingUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['ward_code' => '01-01', 'code' => '01', 'name' => 'Unit 1'],
            ['ward_code' => '01-01', 'code' => '02', 'name' => 'Unit 2'],
            ['ward_code' => '01-02', 'code' => '01', 'name' => 'Unit 1'],
            ['ward_code' => '02-01', 'code' => '01', 'name' => 'Unit 1'],
        ];

        foreach ($units as $unit) {
            $ward = Ward::with('localGovernment')
                ->whereHas('localGovernment', function ($q) use ($unit) {
                    $parts = explode('-', $unit['ward_code']);
                    $q->where('code', $parts[0]);
                })
                ->where('code', explode('-', $unit['ward_code'])[1])
                ->first();

            if ($ward) {
                VotingUnit::create([
                    'ward_id' => $ward->id,
                    'code' => $unit['code'],
                    'name' => $unit['name'],
                    'full_code' => $unit['ward_code'] . '-' . $unit['code']
                ]);
            }
        }
    }
}