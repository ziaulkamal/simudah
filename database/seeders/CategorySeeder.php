<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Rumah Tinggal', 'price' => 50000],
            ['name' => 'Toko', 'price' => 100000],
            ['name' => 'Kantor', 'price' => 150000],
            ['name' => 'Warung', 'price' => 40000],
            ['name' => 'Gudang', 'price' => 120000],
            ['name' => 'Kios', 'price' => 60000],
            ['name' => 'Apartemen', 'price' => 200000],
            ['name' => 'Ruko', 'price' => 180000],
            ['name' => 'Kos', 'price' => 80000],
            ['name' => 'Sekolah', 'price' => 220000],
            ['name' => 'Puskesmas', 'price' => 250000],
            ['name' => 'Klinik', 'price' => 230000],
            ['name' => 'Rumah Sakit', 'price' => 300000],
            ['name' => 'Masjid', 'price' => 0],
            ['name' => 'Gereja', 'price' => 0],
            ['name' => 'Pura', 'price' => 0],
            ['name' => 'Vihara', 'price' => 0],
            ['name' => 'Balai Desa', 'price' => 100000],
            ['name' => 'Gedung Serbaguna', 'price' => 200000],
            ['name' => 'Lapangan', 'price' => 50000],
            ['name' => 'Tempat Parkir', 'price' => 40000],
            ['name' => 'Pabrik', 'price' => 400000],
            ['name' => 'Restoran', 'price' => 250000],
            ['name' => 'Kafe', 'price' => 180000],
            ['name' => 'Studio', 'price' => 160000],
            ['name' => 'Salon', 'price' => 90000],
            ['name' => 'Apotek', 'price' => 150000],
            ['name' => 'Minimarket', 'price' => 130000],
            ['name' => 'Supermarket', 'price' => 300000],
            ['name' => 'Hotel', 'price' => 400000],
            ['name' => 'Penginapan', 'price' => 120000],
            ['name' => 'Bengkel', 'price' => 100000],
            ['name' => 'Laundry', 'price' => 80000],
            ['name' => 'Percetakan', 'price' => 130000],
            ['name' => 'SPBU', 'price' => 500000],
            ['name' => 'Terminal', 'price' => 350000],
            ['name' => 'Stasiun', 'price' => 500000],
            ['name' => 'Bandara', 'price' => 1000000],
            ['name' => 'Pelabuhan', 'price' => 800000],
            ['name' => 'Perumahan', 'price' => 200000],
            ['name' => 'Kawasan Industri', 'price' => 600000],
            ['name' => 'Lahan Kosong', 'price' => 30000],
            ['name' => 'Sawah', 'price' => 20000],
            ['name' => 'Kebun', 'price' => 25000],
            ['name' => 'Hutan Produksi', 'price' => 50000],
            ['name' => 'Pondok Pesantren', 'price' => 120000],
            ['name' => 'Tempat Hiburan', 'price' => 400000],
            ['name' => 'Bioskop', 'price' => 350000],
            ['name' => 'Gedung Pemerintah', 'price' => 500000],
            ['name' => 'Instansi Swasta', 'price' => 250000],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'price' => $category['price'],
                    'status' => 'active',
                ]
            );
        }
    }
}
