<?php

namespace App\Services\Connections;

class Aisc360Service
{
    public function calculate(string $type, array $input): array
    {
        switch (strtolower($type)) {
            case 'moment':
                return $this->calcAiscMoment($input);
            case 'shear':
                return $this->calculateShear($input);
            default:
                return ['status' => 'error', 'message' => 'Loại liên kết chưa hỗ trợ'];
        }
    }

    /**
     * Tính toán Moment Connection theo AISC 360-10 (J3, J4).
     */
    private function calcAiscMoment(array $input): array
    {
        // ==== Input ====
        $method = strtolower($input['method'] ?? 'lrfd'); 
        $M_applied_kNm = floatval($input['Mmax'] ?? 0); // moment demand (kNm)
        $P_applied_kN  = floatval($input['Pmax'] ?? 0); // axial demand (kN)

        $n_bolts = max(1, intval($input['n_bolts'] ?? 1));
        $d = floatval($input['bolt_d_mm'] ?? 20); 
        $fy = floatval($input['fy'] ?? 355); 
        $fu = floatval($input['fu'] ?? 490); 
        $t = floatval($input['plate_t_mm'] ?? 10); 
        $lc = floatval($input['lc_mm'] ?? 20); 
        $lever_arm_mm = floatval($input['lever_arm_mm'] ?? 200);

        // ==== Bolt properties ====
        $bolt_grade = ($input['bolt_grade'] ?? 'A325');
        $grade_map = [
            'A325' => ['Fnt' => 620.0, 'Fnv' => 372.0],
            'A490' => ['Fnt' => 1034.0, 'Fnv' => 600.0],
            'A307' => ['Fnt' => 310.0, 'Fnv' => 188.0],
        ];
        $map = $grade_map[$bolt_grade] ?? $grade_map['A325'];
        $Fnt = $map['Fnt'];
        $Fnv = $map['Fnv'];

        // ==== Strength per bolt ====
        $Ab_mm2 = pi() * pow($d, 2) / 4.0;
        $Rn_bolt_tension_N = $Fnt * $Ab_mm2;
        $Rn_bolt_shear_N   = $Fnv * $Ab_mm2;

        // ==== Bearing limit ====
        $Rn_bearing_by_lc = 1.2 * $lc * $t * $fu;
        $Rn_bearing_by_dt = 2.4 * $d * $t * $fu;
        $Rn_bearing_per_bolt_N = min($Rn_bearing_by_lc, $Rn_bearing_by_dt);

        // ==== Effective bolt strength ====
        $Rn_effective_per_bolt_N = min($Rn_bolt_shear_N, $Rn_bearing_per_bolt_N);

        // ==== Group force ====
        $phi_bolt = ($method === 'lrfd') ? 0.75 : null;
        $omega    = ($method === 'asd') ? 2.0 : null;

        if ($method === 'lrfd') {
            $group_force_N = $n_bolts * ($phi_bolt * $Rn_effective_per_bolt_N);
        } else {
            $group_force_N = $n_bolts * ($Rn_effective_per_bolt_N / $omega);
        }

        // ==== Moment capacity ====
        $phiMn = ($group_force_N * $lever_arm_mm) / 1e6; // kNm
        $phiPn = ($method === 'lrfd') ? 0.9 * $Rn_bolt_tension_N * $n_bolts / 1000.0
                                    : ($Rn_bolt_tension_N * $n_bolts / $omega / 1000.0); // kN

        // ==== Interaction (AISC 360, giản lược) ====
        $interactionRatio = 0.0;
        if ($phiMn > 0 && $phiPn > 0) {
            $interactionRatio = ($M_applied_kNm / $phiMn) + ($P_applied_kN / $phiPn);
        }

        $status = $interactionRatio <= 1.0 ? 'OK' : 'NG';

        return [
            'status' => $status,
            'method' => strtoupper($method),
            'M_applied_kNm' => $M_applied_kNm,
            'P_applied_kN'  => $P_applied_kN,
            'phiMn' => $phiMn,
            'phiPn' => $phiPn,
            'interactionRatio' => $interactionRatio,
            'n_bolts' => $n_bolts,
            'bolt_grade' => $bolt_grade,
            'Rn_bolt_tension_N' => $Rn_bolt_tension_N,
            'Rn_bolt_shear_N' => $Rn_bolt_shear_N,
            'Rn_bearing_N' => $Rn_bearing_per_bolt_N,
            'group_force_N' => $group_force_N,
        ];
    }

    private function calculateShear(array $input): array
    {
        // TODO: shear logic sau
        return [
            'status' => 'OK',
            'note'   => 'Shear check AISC 360-10',
        ];
    }
}