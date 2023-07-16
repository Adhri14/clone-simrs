<?php

namespace Database\Seeders;

use App\Models\CategoryAdmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            [
                "name" => "Dokter",
                "slug" => "dokter",
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "name" => "Pengumuman",
                "slug" => "pengumuman",
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
