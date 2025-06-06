<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client; // Import model Client
use App\Models\User; // Import model User

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user admin pertama sebagai pembuat data (opsional)
        $adminUser = User::where('role', 'Admin')->first();

        Client::create([
            'client_name' => 'PT Maju Jaya',
            'contact_person_name' => 'Bapak Budi',
            'contact_email' => 'budi@majujaya.com',
            'contact_phone' => '081234567890',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'created_by_user_id' => $adminUser ? $adminUser->id : null,
        ]);
        Client::create([
            'client_name' => 'CV Mundur Teratur',
            'contact_person_name' => 'Ibu Siti',
            'contact_email' => 'siti@mundurteratur.co.id',
            'contact_phone' => '081298765432',
            'address' => 'Jl. Thamrin No. 10, Jakarta',
            'created_by_user_id' => $adminUser ? $adminUser->id : null,
        ]);
    }
}