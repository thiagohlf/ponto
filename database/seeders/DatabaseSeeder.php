<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            SystemSeeder::class,
            TimeRecordSeeder::class,
            MedicalCertificateSeeder::class,
            AbsenceSeeder::class,
            OvertimeSeeder::class,
            ApprovalSeeder::class,
            LocationSeeder::class,
        ]);
    }
}
