<?php

namespace Tests\Feature;

use App\Models\StrcModel;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class AnalysisControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_analysis_success()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $model = StrcModel::factory()->create(['project_id' => $project->id, 'file_path' => 'dummy.sdb']);

        $mockProcess = $this->mock(Process::class, function ($mock) {
            $mock->shouldReceive('mustRun')->andReturnNull();
            $mock->shouldReceive('getOutput')->andReturn(json_encode([
                'load_case' => 'Combo1',
                'joints' => ['Joint1' => ['UX' => 0.1, 'UY' => 0.2]],
                'frames' => ['Frame1' => ['P' => [100], 'V2' => [50], 'M2' => [200]]],
                'total_joints' => 1,
                'total_frames' => 1,
            ]));
        });

        $response = $this->actingAs($user)->postJson("/api/analysis/{$model->id}/run", ['load_case' => 'Combo1']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('analysis_runs', ['model_id' => $model->id, 'status' => 'success']);
        $this->assertDatabaseHas('results', ['summary->total_joints' => 1]);
        $this->assertDatabaseHas('result_items', ['element_ref' => 'Joint1', 'type' => 'joint_displacement']);
        $this->assertDatabaseHas('result_items', ['element_ref' => 'Frame1', 'type' => 'frame_force', 'max_moment' => 200]);
    }
}