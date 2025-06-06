<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::where('client_name', 'PT Maju Jaya')->first();
        $client2 = Client::where('client_name', 'CV Mundur Teratur')->first();
        $adminUser = User::where('role', 'Admin')->first();
        $staffUser1 = User::where('email', 'staff1@djokihub.com')->first(); // Asumsi ada staff dengan email ini
        $staffUser2 = User::where('email', 'staff2@djokihub.com')->first(); // Asumsi ada staff dengan email ini

        if ($client1 && $adminUser) {
            $project1 = Project::create([
                'project_name' => 'Website E-commerce Klien A',
                'client_id' => $client1->id,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'status' => 'On-going',
                'budget' => 15000000,
                'payment_status' => 'DP',
                'description' => 'Pembuatan website e-commerce lengkap dengan payment gateway.',
                'created_by_user_id' => $adminUser->id,
                'order_id' => '#PRJ-001'
            ]);
            if ($staffUser1) {
                $project1->staff()->attach($staffUser1->id); // Menggunakan relasi staff()
            }
        }

        if ($client2 && $adminUser) {
            $project2 = Project::create([
                'project_name' => 'Desain Logo Perusahaan B',
                'client_id' => $client2->id,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'status' => 'Pending',
                'budget' => 5000000,
                'payment_status' => 'Unpaid',
                'description' => 'Desain logo baru untuk rebranding perusahaan.',
                'created_by_user_id' => $adminUser->id,
                'order_id' => '#PRJ-002'
            ]);
             if ($staffUser1 && $staffUser2) {
                $project2->staff()->attach([$staffUser1->id, $staffUser2->id]);
            }
        }
    }
}