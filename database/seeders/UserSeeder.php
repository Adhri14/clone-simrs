<?php

namespace Database\Seeders;

use App\Models\BPJS\Antrian\PoliklinikAntrian;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            "name" => "Admin IT",
            "email" => "adminit@gmail.com",
            "username" => "adminit",
            "phone" => "089529909036",
            'password' => bcrypt('qweqwe123'),
            'user_id' => 2,
            'email_verified_at' => now()

        ]);
        $user->assignRole('Admin');
        $user = User::create([
            "name" => "Admin Super",
            "email" => "adminrs@gmail.com",
            "username" => "adminrs",
            "phone" => "089529909036",
            'password' => bcrypt('qweqwe123'),
            'user_id' => 2,
            'email_verified_at' => now()
        ]);
        $user->assignRole('Admin Super');
        $user = User::create([
            "name" => "Marwan Dhiaur Rahman",
            "email" => "marwandhiaurrahman@gmail.com",
            "username" => "marwan",
            "phone" => "089529909036",
            'password' => bcrypt('qweqwe123'),
        ]);
        $user->assignRole('Admin Super');
        $user = User::create([
            "name" => "Admin Antrian BPJS",
            "email" => "antrianbpjs@gmail.com",
            "username" => "antrianbpjs",
            "phone" => "089529909036",
            'password' => bcrypt('antrianbpjs'),
            'user_id' => 2,
            'email_verified_at' => now()
        ]);
        $user->assignRole('Admin Super');
        $user = User::create([
            "name" => "Bagian Umum",
            "email" => "bagum@gmail.com",
            "username" => "bagianumum",
            "phone" => "089529909036",
            'password' => bcrypt('bagianumum'),
        ]);
        $user->assignRole('Bagian Umum');
        $user = User::create([
            "name" => "Admin Pendaftaran",
            "email" => "adminpendaftaran@gmail.com",
            "username" => "adminpendaftaran",
            "phone" => "089529909036",
            'password' => bcrypt('adminpendaftaran'),
        ]);
        $user->assignRole('Pendaftaran');
        $user = User::create([
            "name" => "Admin Kasir",
            "email" => "adminkasir@gmail.com",
            "username" => "adminkasir",
            "phone" => "089529909036",
            'password' => bcrypt('adminkasir'),
        ]);
        $user->assignRole('Kasir');
        $user = User::create([
            "name" => "Admin Poliklinik",
            "email" => "adminpoli@gmail.com",
            "username" => "adminpoli",
            "phone" => "089529909036",
            'password' => bcrypt('adminpoli'),
        ]);
        $user->assignRole('Poliklinik');
        $user = User::create([
            "name" => "Admin Farmasi",
            "email" => "adminfarmasi@gmail.com",
            "username" => "adminfarmasi",
            "phone" => "089529909036",
            'password' => bcrypt('adminfarmasi'),
        ]);
        $user->assignRole('Farmasi');
        $user = User::create([
            "name" => "Admin Pelayanan Medis",
            "email" => "adminyanmed@gmail.com",
            "username" => "adminyanmed",
            "phone" => "089529909036",
            'password' => bcrypt('adminyanmed'),
        ]);
        $user->assignRole('Pelayanan Medis');
        $user = User::create([
            "name" => "Admin Rekam Medis",
            "email" => "adminrekammedis@gmail.com",
            "username" => "adminrekammedis",
            "phone" => "089529909036",
            'password' => bcrypt('adminrekammedis'),
        ]);
        $user->assignRole('Rekam Medis');

        $adminpoli = [
            'OBG',
            // 'HIV',
            'KLT',
            'PAR',
            // 'MCU',
            'ORT',
            'URO',
            'JAN',
            'BSY',
            'INT',
            'THT',
            'MAT',
            '008',
            'ANT',
            'BED',
            'GIG',
            'GND',
            'GPR',
            'IRM',
            'ANA',
            'SAR',
            'JIW',
            '020',

        ];
        foreach ($adminpoli as  $value) {
            $poli = PoliklinikAntrian::where('kodeSubspesialis', $value)->first();
            $user = User::create([
                "name" => "ADMIN " . $poli->namaSubspesialis,
                "email" => $value . "@gmail.com",
                "username" => $value,
                "phone" => '089529909036',
                'password' => bcrypt('adminpoli'),
                'user_id' => 2,
                'email_verified_at' => now()
            ]);
            $user->assignRole('Poliklinik');
        }
    }
}
