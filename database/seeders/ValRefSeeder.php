<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ValRefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = [
            ['name' => 'Technology'],
            ['name' => 'Finance'],
            ['name' => 'Healthcare'],
            ['name' => 'Education'],
            ['name' => 'Retail'],
        ];

        foreach ($sectors as  $sector) {
            Sector::create($sector);
        }
    }
}
