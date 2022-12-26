<?php

namespace Database\Seeders;

use App\Http\Controllers\BPJS\Antrian\AntrianController;
use App\Models\BPJS\Antrian\PoliklinikAntrian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoliklinikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $api = new AntrianController();
        $response = $api->ref_poli();
        if ($response->status() == 200) {
            $polikliniks = $response->getData()->response;
            foreach ($polikliniks as $value) {
                PoliklinikAntrian::firstOrCreate([
                    'kodePoli' => $value->kdpoli,
                    'namaPoli' => $value->nmpoli,
                    'kodeSubspesialis' => $value->kdsubspesialis,
                    'namaSubspesialis' => $value->nmsubspesialis,
                ]);
            }
        } else {
            return $response->getData()->metadata->message;
        }
    }
}
