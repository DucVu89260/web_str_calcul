<?php

namespace Tests\Unit\Services\Connections;

use Tests\TestCase;
use App\Services\Connections\Aisc360Service;

class Aisc360ServiceTest extends TestCase
{
    public function test_moment_calculation_basic()
    {
        $service = new Aisc360Service();

        $result = $service->calculate('moment', [
            'Mmax' => 200,  // kNm
            'Pmax' => 100,  // kN
            'n_bolts' => 8,
            'bolt_d_mm' => 22,
            'plate_t_mm' => 20,
            'lever_arm_mm' => 400,
            'fy' => 345,
            'fu' => 490,
            'bolt_grade' => 'A325',
            'method' => 'lrfd',
        ]);

        $this->assertEquals('OK', $result['status']);
        $this->assertArrayHasKey('phiMn', $result);
        $this->assertArrayHasKey('phiPn', $result);
        $this->assertArrayHasKey('interactionRatio', $result);
    }

    public function test_invalid_type()
    {
        $service = new Aisc360Service();

        $result = $service->calculate('invalid', []);

        $this->assertEquals('error', $result['status']);
    }

    public function test_failure_due_to_high_load()
    {
        $service = new Aisc360Service();

        $result = $service->calculate('moment', [
            'Mmax' => 2000, // quá lớn
            'Pmax' => 500,  // quá lớn
        ]);

        $this->assertEquals('NG', $result['status']); // không đạt
        $this->assertGreaterThan(1.0, $result['interactionRatio']); // IR > 1.0
    }

    public function test_edge_case_small_moment_large_axial()
    {
        $service = new Aisc360Service();

        $result = $service->calculate('moment', [
            'Mmax' => 10,
            'Pmax' => 900,
        ]);

        $this->assertArrayHasKey('interactionRatio', $result);
        $this->assertIsNumeric($result['interactionRatio']);
    }
}
