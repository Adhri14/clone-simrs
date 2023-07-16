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
            "name" => "Admin RSUD",
            "email" => "admin.rsud@lamaddukelleng.com",
            "username" => "adminrs",
            "phone" => "081234567890",
            'password' => bcrypt('P@ssw0rd4341&*'),
            'user_verify' => 1,
            'email_verified_at' => now()
        ]);
        $user->assignRole('Admin Super');
    }
}
