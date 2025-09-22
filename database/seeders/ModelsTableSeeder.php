<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('models')->insert([
            'project_id' => 1,
            'created_by' => 1,
            'snapshot_file_id' => null,
            'name' => 'Test SAP2000 Model',
            'file_path' => 'C:\\Users\\ducvu\\Desktop\\test-api\\test-api.sdb',
            'version' => 1,
            'status' => 'draft',
            'checksum' => null,
            'snapshot_meta' => json_encode(['note' => 'seeded for test']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
