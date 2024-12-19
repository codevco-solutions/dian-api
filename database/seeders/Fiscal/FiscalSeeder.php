<?php

namespace Database\Seeders\Fiscal;

use Illuminate\Database\Seeder;

class FiscalSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DianResolutionSeeder::class,
            TaxRuleSeeder::class,
            DocumentNumberingConfigSeeder::class,
        ]);
    }
}
