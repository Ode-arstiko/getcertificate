<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('users')->insert([
            'name' => 'admin1',
            'email' => 'admin1@gmail.com',
            'password' => bcrypt('123123123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}