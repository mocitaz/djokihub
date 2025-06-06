<?php

namespace Database\Seeders;

use App\Models\Regulation;
use App\Models\Sop;
use App\Models\SopCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Required if using DB facade

class WorkRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean up existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // For MySQL, adjust for other DBs
        Sop::truncate();
        SopCategory::truncate();
        Regulation::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // Create SOP Categories and SOPs
        $category1 = SopCategory::create([
            'name' => '1. General Procedures',
            'introduction' => '<p>These Standard Operating Procedures (SOPs) are designed to ensure consistency and efficiency in our daily operations. All employees are required to familiarize themselves with these procedures and follow them accordingly.</p>',
            'order' => 1,
        ]);

        Sop::create([
            'sop_category_id' => $category1->id,
            'title' => '1.1 Daily Operations',
            'description' => '<p>Start your workday by checking your assigned tasks and priorities. Update your status in the team management system and attend the daily stand-up meeting at 9:30 AM.</p>',
            'order' => 1,
        ]);

        Sop::create([
            'sop_category_id' => $category1->id,
            'title' => '1.2 Communication Protocol',
            'description' => '<p>Use appropriate communication channels based on urgency and context. Slack for quick questions, email for formal communications, and Zoom for meetings.</p><p>Ensure all project-related communication is documented in the respective project channel or task.</p>',
            'order' => 2,
        ]);

        $category2 = SopCategory::create([
            'name' => '2. Safety Guidelines',
            'introduction' => '<p>Maintaining a safe work environment is paramount. Adherence to these safety guidelines protects all team members.</p>',
            'order' => 2,
        ]);

        Sop::create([
            'sop_category_id' => $category2->id,
            'title' => '2.1 Workplace Safety',
            'description' => '<p>Ensure your workspace is clean and organized. Report any safety concerns (e.g., trip hazards, faulty equipment) to the facility management team or your direct supervisor immediately.</p>',
            'order' => 1,
        ]);


        // Create Regulations
        Regulation::create([
            'title' => 'REG-001: Data Security Policy',
            'description' => '<p>All employees must adhere to the company\'s data security policy, ensuring confidential information is protected at all times. Unauthorized sharing or distribution of sensitive data is strictly prohibited and may result in disciplinary action.</p><ul><li>Use strong, unique passwords.</li><li>Enable two-factor authentication where available.</li><li>Do not leave sensitive documents unattended.</li></ul>',
            'order' => 1,
        ]);

        Regulation::create([
            'title' => 'REG-002: Code of Conduct',
            'description' => '<p>Employees are expected to maintain a professional and respectful work environment. Harassment, discrimination, or any form of misconduct will not be tolerated. Treat all colleagues, clients, and partners with respect and integrity.</p>',
            'order' => 2,
        ]);
    }
}