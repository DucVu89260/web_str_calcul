<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectParameter;
use App\Models\PreliminarySuggestion;
use App\Models\Section; // Giả sử bạn có Model Section với data mẫu

class PreliminarySuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Giả sử có project ID 1 (từ DB gốc của bạn, có project_id=1)
        $project = Project::find(1);
        if (!$project) {
            // Tạo project mẫu nếu chưa có
            $project = Project::create([
                'name' => 'Sample Project for Preliminary',
                'description' => 'Test preliminary suggestions',
                'default_unit_system' => 'SI',
                'visibility' => 'private',
                'preliminary_status' => 'draft',
            ]);
        }

        // Nạp parameters thủ công cho project
        $params = ProjectParameter::updateOrCreate(
            ['project_id' => $project->id],
            [
                'location' => 'Hanoi, VN',
                'dead_load_roof' => 1.5, // kN/m²
                'live_load_roof' => 0.75, // kN/m²
                'eave_height' => 8.0, // m
                'total_spans' => 2,
                'max_span' => 20.0, // m
                'has_crane' => true,
                'crane_details' => json_encode([
                    'crane_weight' => 10, // tons
                    'hoist_weight' => 5, // tons
                    'mode' => 'A3', // chế độ làm việc
                    'count' => 1,
                ]),
                'extra_params' => json_encode([
                    'wind_speed' => 120, // km/h từ standards
                    'seismic_zone' => 'Zone 2',
                ]),
            ]
        );

        // Nạp sections mẫu nếu chưa có (thủ công)
        $section1 = Section::firstOrCreate([
            'project_id' => $project->id,
            'name' => 'H-Beam 300x150',
            'type' => 'steel_beam',
            'properties' => json_encode(['depth' => 300, 'width' => 150, 'Ix' => 1.2e6]), // mm, cm4, etc.
        ]);

        $section2 = Section::firstOrCreate([
            'project_id' => $project->id,
            'name' => 'I-Beam 400x200',
            'type' => 'steel_beam',
            'properties' => json_encode(['depth' => 400, 'width' => 200, 'Ix' => 2.5e6]),
        ]);

        // Nạp suggestions thủ công (với "công thức" trong meta, ví dụ công thức moment đơn giản)
        PreliminarySuggestion::create([
            'project_id' => $project->id,
            'suggested_section_id' => $section1->id,
            'similarity_score' => 85.5,
            'meta' => json_encode([
                'from_project' => 'Old Project A',
                'reason' => 'Similar span and loads',
                'manual_formula' => 'Moment = (dead_load + live_load) * max_span^2 / 8', // Công thức thủ công
            ]),
        ]);

        PreliminarySuggestion::create([
            'project_id' => $project->id,
            'suggested_section_id' => $section2->id,
            'similarity_score' => 92.0,
            'meta' => json_encode([
                'from_project' => 'Old Project B',
                'reason' => 'High crane compatibility',
                'manual_formula' => 'Shear = crane_weight * g / 2', // Công thức thủ công khác
            ]),
        ]);

        // Update project meta với suggestions
        $project->update([
            'preliminary_status' => 'suggested',
            'preliminary_meta' => PreliminarySuggestion::where('project_id', $project->id)->get(),
        ]);

        $this->command->info('Preliminary suggestions seeded successfully!');
    }
}