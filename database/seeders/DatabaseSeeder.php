<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AddressTypeSeeder::class,
            PromotionSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
