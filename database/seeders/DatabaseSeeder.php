<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Sekolah',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Budi Guru',
            'username' => 'guru',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        User::factory()->create([
            'name' => 'Aisyah Murid',
            'username' => 'murid',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);
    }
}
