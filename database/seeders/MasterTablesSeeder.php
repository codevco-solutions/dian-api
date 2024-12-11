<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\DianMasterTablesSeeder;

class MasterTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountriesTableSeeder::class,
            DianMasterTablesSeeder::class,
        ]);
    }
}
