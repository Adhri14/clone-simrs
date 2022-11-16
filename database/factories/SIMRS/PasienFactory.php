<?php

namespace Database\Factories\SIMRS;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SIMRS\Pasien>
 */
class PasienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // identifier
            'norm' => fake()->numerify('A######'),
            'nokartu' => fake()->numerify('00000########'),
            'nik' => fake()->numerify('320000##########'),
            // name
            'nama' => fake()->name(),
            // telecom
            'nohp' => fake()->phoneNumber(),
            'email' => fake()->email(),
            // address
            'negara' => 'ID',
            'provinsi' => 32,
            'kota' => fake()->numerify('320#'),
            'alamat' => fake()->streetAddress(),
            'kodepos' => fake()->postcode(),
            // photo
            // pasien
            'jeniskelamin' => fake()->randomElement(['male', 'female']),
            'tanggallahir' => fake()->date(),
            'menikah' => fake()->randomElement(['0', '1']),
            // keterangan tambahan
            'nokk' => fake()->numerify('320000##########'),
        ];
    }
}
