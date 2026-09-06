<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Per README: transactional tables (fin_documents, saob_lines, etc.)
     * start empty on purpose. This seeder creates user accounts only.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'kaustria@mmsu.edu.ph'],
            [
                'name' => 'K. Austria',
                'position' => 'Budget Officer',
                'password' => Hash::make('password'),
            ]
        );
    }
}
