<?php

namespace Database\Seeders;

use App\Models\Drug;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drugs = [
            // Obat Bebas - category_id = 1
            [
                'name' => 'Promag Tablet',
                'barcode' => null,
                'description' => 'Obat maag dan asam lambung',
                'price' => 10000,
                'purchase_price' => 7500,
                'modal' => 8000,
                'stock' => 120,
                'category_id' => 2,
                'expired_at' => Carbon::parse('2025-11-10'),
                'packaging_types' => 'strip',
                'image_path' => null,
            ],
            [
                'name' => 'Paramex',
                'barcode' => null,
                'description' => 'Obat sakit kepala dan flu',
                'price' => 11000,
                'purchase_price' => 8500,
                'modal' => 9000,
                'stock' => 85,
                'category_id' => 2,
                'expired_at' => Carbon::parse('2026-02-10'),
                'packaging_types' => 'strip',
                'image_path' => null,
            ],

            // Obat Bebas Terbatas - category_id = 2
            [
                'name' => 'OBH Combi Syrup 100ml',
                'barcode' => null,
                'description' => 'Obat batuk hitam',
                'price' => 18000,
                'purchase_price' => 14500,
                'modal' => 15000,
                'stock' => 50,
                'category_id' => 3,
                'expired_at' => Carbon::parse('2026-03-20'),
                'packaging_types' => 'botol',
                'image_path' => null,
            ],

            // Obat Keras - category_id = 3
            [
                'name' => 'Amoxicillin 500mg',
                'barcode' => null,
                'description' => 'Antibiotik untuk infeksi bakteri',
                'price' => 15000,
                'purchase_price' => 12000,
                'modal' => 12500,
                'stock' => 60,
                'category_id' => 4,
                'expired_at' => Carbon::parse('2025-10-15'),
                'packaging_types' => 'strip',
                'image_path' => null,
            ],

            // Jamu - category_id = 4
            [
                'name' => 'Tolak Angin Cair',
                'barcode' => null,
                'description' => 'Jamu herbal untuk masuk angin',
                'price' => 12000,
                'purchase_price' => 9000,
                'modal' => 9500,
                'stock' => 100,
                'category_id' => 5,
                'expired_at' => Carbon::parse('2026-01-01'),
                'packaging_types' => 'sachet',
                'image_path' => null,
            ],
            [
                'name' => 'Neurobion',
                'barcode' => null,
                'description' => 'Vitamin B kompleks',
                'price' => 28000,
                'purchase_price' => 25000,
                'modal' => 24500,
                'stock' => 60,
                'category_id' => 3,
                'expired_at' => now()->addMonths(18),
                'packaging_types' => 'tablet',
                'image_path' => null,
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'barcode' => null,
                'description' => 'Antibiotik untuk infeksi bakteri',
                'price' => 15000,
                'purchase_price' => 12000,
                'modal' => 11800,
                'stock' => 90,
                'category_id' => 4,
                'expired_at' => now()->addMonths(9),
                'packaging_types' => 'tablet',
                'image_path' => null,
            ],
            [
                'name' => 'Tolak Angin',
                'barcode' => null,
                'description' => 'Obat herbal masuk angin',
                'price' => 6000,
                'purchase_price' => 5000,
                'modal' => 4800,
                'stock' => 150,
                'category_id' => 5,
                'expired_at' => now()->addMonths(14),
                'packaging_types' => 'sachet',
                'image_path' => null,
            ],
            [
                'name' => 'Bodrex',
                'barcode' => null,
                'description' => 'Obat sakit kepala dan flu',
                'price' => 8500,
                'purchase_price' => 7500,
                'modal' => 7400,
                'stock' => 110,
                'category_id' => 2,
                'expired_at' => now()->addMonths(11),
                'packaging_types' => 'strip',
                'image_path' => null,
            ],
            [
                'name' => 'Konidin',
                'barcode' => null,
                'description' => 'Obat batuk dan flu',
                'price' => 9500,
                'purchase_price' => 8300,
                'modal' => 8200,
                'stock' => 95,
                'category_id' => 3,
                'expired_at' => now()->addMonths(8),
                'packaging_types' => 'strip',
                'image_path' => null,
            ],
            [
                'name' => 'Paracetamol 500mg',
                'barcode' => null,
                'description' => 'Obat penurun panas dan nyeri',
                'price' => 5000,
                'purchase_price' => 4000,
                'modal' => 3900,
                'stock' => 200,
                'category_id' => 2,
                'expired_at' => now()->addMonths(12),
                'packaging_types' => 'tablet',
                'image_path' => null,
            ],
            [
                'name' => 'Dextral',
                'barcode' => null,
                'description' => 'Obat batuk dan pilek',
                'price' => 8500,
                'purchase_price' => 7300,
                'modal' => 7200,
                'stock' => 75,
                'category_id' => 3,
                'expired_at' => now()->addMonths(9),
                'packaging_types' => 'strip',
                'image_path' => null,
            ],
            [
                'name' => 'Antangin',
                'barcode' => null,
                'description' => 'Obat herbal pegal linu dan masuk angin',
                'price' => 6000,
                'purchase_price' => 5000,
                'modal' => 4900,
                'stock' => 130,
                'category_id' => 5,
                'expired_at' => now()->addMonths(15),
                'packaging_types' => 'sachet',
                'image_path' => null,
            ],
            [
                'name' => 'Panadol Merah',
                'barcode' => '899999000001',
                'description' => 'Panadol Merah untuk meredakan sakit kepala dan nyeri ringan.',
                'price' => 8000,
                'purchase_price' => 6000,
                'modal' => 6000,
                'stock' => 100,
                'category_id' => 2, // Obat Bebas
                'expired_at' => now()->addMonths(12),
                'packaging_types' => 'strip',
                'image_path' => 'drugs/panadol-merah.jpg',
            ],
            [
                'name' => 'Panadol Biru',
                'barcode' => '899999000002',
                'description' => 'Panadol Biru efektif untuk demam dan nyeri otot.',
                'price' => 9000,
                'purchase_price' => 6500,
                'modal' => 6500,
                'stock' => 120,
                'category_id' => 2, // Obat Bebas
                'expired_at' => now()->addMonths(10),
                'packaging_types' => 'strip',
                'image_path' => 'drugs/panadol-biru.jpg',
            ],
            [
                'name' => 'Panadol Hijau',
                'barcode' => '899999000003',
                'description' => 'Panadol Hijau mengandung ekstra herbal untuk meredakan nyeri.',
                'price' => 9500,
                'purchase_price' => 7000,
                'modal' => 7000,
                'stock' => 80,
                'category_id' => 2, // Obat Bebas
                'expired_at' => now()->addMonths(8),
                'packaging_types' => 'strip',
                'image_path' => 'drugs/panadol-hijau.jpg',
            ],
        ];

        foreach ($drugs as $data) {
            if (!Drug::where('name', $data['name'])->exists()) {
                Drug::create($data);
            }
        }
    }
}
