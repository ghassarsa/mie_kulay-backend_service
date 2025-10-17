<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'staff@example.com',
            'password' => '123',
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'owner@example.com',
            'password' => '123',
            'role' => 'owner',
        ]);
        User::create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
            'password' => '123',
            'role' => 'admin',
        ]);

        Kategori::insert([
            ['jenis_hidangan' => 'makanan'],
            ['jenis_hidangan' => 'minuman'],
            ['jenis_hidangan' => 'topping'],
        ]);
    }
}
