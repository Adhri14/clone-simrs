<?php

namespace Database\Seeders;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Models\BPJS\Antrian\DokterAntrian;
use App\Models\Dokter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DokterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $api = new AntrianController();
        $response = $api->ref_dokter();
        if ($response->status() == 200) {
            $dokters = $response->getData()->response;
            foreach ($dokters as $value) {
                DokterAntrian::firstOrCreate([
                    'kodeDokter' => $value->kodedokter,
                    'namaDokter' => $value->namadokter,
                ]);
            }
        } else {
            return $response->getData()->metadata->message;
        }
    }
}
