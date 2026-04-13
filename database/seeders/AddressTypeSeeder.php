<?php

namespace Database\Seeders;

use App\Models\AddressType;
use Illuminate\Database\Seeder;

class AddressTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Residential Address',    'status' => 'active'],
            ['name' => 'Correspondence Address',  'status' => 'active'],
            ['name' => 'Business Address',        'status' => 'active'],
            ['name' => 'Mailing Address',         'status' => 'active'],
        ];

        foreach ($types as $type) {
            AddressType::firstOrCreate(['name' => $type['name']], $type);
        }

        $this->command->info('Address types seeded.');
    }
}
