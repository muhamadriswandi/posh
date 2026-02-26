<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentChannel;

class PaymentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentChannel::insert([
            ['name' => 'Teller', 'keywords' => json_encode(['teller', 'cash']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ATM', 'keywords' => json_encode(['atm', 'tarik tunai']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'EDC', 'keywords' => json_encode(['edc']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mobile Banking', 'keywords' => json_encode(['m-banking', 'mbanking', 'transfer']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'QRIS', 'keywords' => json_encode(['qris']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'E-Commerce', 'keywords' => json_encode(['shopee', 'tokopedia', 'lazada']), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
