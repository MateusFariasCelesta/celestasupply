<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Mateus Farias',
            'email'    => 'mateus.farias@celestamineracao.com.br',
            'password' => Hash::make('admin1234_'),
            'role'     => 'admin',
            'isActive' => true,
        ]);
    }
}
