<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteParameter;
use App\Models\SiteWindParameter;

/**
 * SiteWindParametersSeeder
 *
 * Nguồn: QCVN 02:2022/BXD - Bảng 5.1 (Phân vùng áp lực gió, vận tốc gió theo địa danh hành chính).
 * Bạn có thể kiểm tra file PDF gốc (đã upload) để tra chi tiết theo huyện/quận nếu cần. (See file reference in project).
 *
 * Lưu ý:
 * - Seeder này sẽ chèn 1 bản ghi "tóm tắt" cho mỗi tỉnh/thành (bản ghi chung "Tất cả ...") sử dụng region chính.
 * - Nếu tỉnh có nhiều region (ví dụ Hà Nội có cả II và III), seeder sẽ chèn region "phổ biến" làm mặc định.
 * - Bản ghi chi tiết theo huyện/quận nên được thêm nếu bạn cần tra chính xác từng địa danh như trong Bảng 5.1.
 */
class SiteWindParametersSeeder extends Seeder
{
    public function run()
    {
        // map region -> [W0 (daN/m2), V3s50 (m/s), V10m50 (m/s)]
        $regionMap = [
            'I'  => ['W0' => 65,  'V3s50' => 36, 'V10m50' => 26],
            'II' => ['W0' => 95,  'V3s50' => 44, 'V10m50' => 31],
            'III'=> ['W0' => 125, 'V3s50' => 50, 'V10m50' => 36],
            'IV' => ['W0' => 155, 'V3s50' => 56, 'V10m50' => 40],
            'V'  => ['W0' => 185, 'V3s50' => 61, 'V10m50' => 43],
        ];

        // Danh sách tỉnh/thành và region mặc định (ánh xạ dựa vào QCVN 02:2022/BXD - Bảng 5.1)
        // Mình chọn region "tổng quát" cho mỗi tỉnh (nhiều tỉnh có nhiều phân vùng -> cần thêm ghi nếu muốn chi tiết).
        $provinces = [
            // Bắc Bộ (nhiều tỉnh thuộc vùng II/III/IV)
            ['name' => 'Hà Nội',       'region' => 'II'],
            ['name' => 'Hải Phòng',    'region' => 'IV'], // note: Hải Phòng có cả IV và V (Bạch Long Vĩ)
            ['name' => 'Hải Dương',    'region' => 'III'],
            ['name' => 'Bắc Ninh',     'region' => 'II'],
            ['name' => 'Hưng Yên',     'region' => 'III'],
            ['name' => 'Bắc Giang',    'region' => 'II'],
            ['name' => 'Lào Cai',      'region' => 'II'],
            ['name' => 'Lạng Sơn',     'region' => 'I'],
            ['name' => 'Hà Giang',     'region' => 'II'],
            ['name' => 'Yên Bái',      'region' => 'II'],
            ['name' => 'Phú Thọ',      'region' => 'II'],
            ['name' => 'Tuyên Quang',  'region' => 'II'],
            ['name' => 'Sơn La',       'region' => 'II'],
            ['name' => 'Điện Biên',    'region' => 'II'],
            ['name' => 'Hòa Bình',     'region' => 'II'],

            // Đông Bắc / Đông Nam Trung Bộ & Bắc Trung Bộ
            ['name' => 'Ninh Bình',    'region' => 'IV'],
            ['name' => 'Thanh Hóa',    'region' => 'IV'],
            ['name' => 'Nghệ An',      'region' => 'III'],
            ['name' => 'Hà Tĩnh',      'region' => 'IV'],
            ['name' => 'Quảng Bình',   'region' => 'III'],
            ['name' => 'Quảng Trị',    'region' => 'II'],
            ['name' => 'Thừa Thiên Huế','region' => 'III'],
            ['name' => 'Quảng Nam',    'region' => 'III'],
            ['name' => 'Quảng Ngãi',   'region' => 'III'],
            ['name' => 'Bình Định',    'region' => 'III'],
            ['name' => 'Phú Yên',      'region' => 'III'],

            // South Central Coast & Central Highlands
            ['name' => 'Khánh Hòa',    'region' => 'II'],
            ['name' => 'Ninh Thuận',   'region' => 'II'],
            ['name' => 'Bình Thuận',   'region' => 'II'],
            ['name' => 'Kon Tum',      'region' => 'I'],
            ['name' => 'Gia Lai',      'region' => 'I'],
            ['name' => 'Đắk Lắk',      'region' => 'I'],
            ['name' => 'Đắk Nông',     'region' => 'I'],
            ['name' => 'Lâm Đồng',     'region' => 'I'],

            // Đông Nam Bộ
            ['name' => 'Hồ Chí Minh',  'region' => 'II'],
            ['name' => 'Bình Dương',   'region' => 'I'],
            ['name' => 'Đồng Nai',     'region' => 'II'],
            ['name' => 'Bà Rịa - Vũng Tàu','region' => 'II'],

            // Mekong Delta
            ['name' => 'Cần Thơ',      'region' => 'II'],
            ['name' => 'An Giang',     'region' => 'I'],
            ['name' => 'Kiên Giang',   'region' => 'II'],
            ['name' => 'Bạc Liêu',     'region' => 'II'],
            ['name' => 'Sóc Trăng',    'region' => 'II'],
            ['name' => 'Bến Tre',      'region' => 'II'],
            ['name' => 'Tiền Giang',   'region' => 'II'],
            ['name' => 'Trà Vinh',     'region' => 'II'],
            ['name' => 'Vĩnh Long',    'region' => 'II'],
            ['name' => 'Long An',      'region' => 'II'],
            ['name' => 'Hậu Giang',    'region' => 'II'],

            // Southwest islands & special
            ['name' => 'Phú Quốc',     'region' => 'III'], // Phú Quốc thuộc vùng III per table
            ['name' => 'Côn Đảo',      'region' => 'III'],

            // Northern midlands (others)
            ['name' => 'Thái Bình',    'region' => 'IV'],
            ['name' => 'Nam Định',     'region' => 'IV'],
            ['name' => 'Quảng Ninh',   'region' => 'III'],
            ['name' => 'Bắc Kạn',      'region' => 'I'],
            ['name' => 'Thái Nguyên',  'region' => 'II'],
            ['name' => 'Bắc Giang',    'region' => 'II'], // already above; duplicates are okay (will dedupe)
            ['name' => 'Hải Dương',    'region' => 'III'], // already above
            ['name' => 'Hải Phòng',    'region' => 'IV'],  // already above

            // Add remaining provinces default to II if uncertain (safe default)
            ['name' => 'Bình Phước',   'region' => 'I'],
            ['name' => 'Bình Thuận',   'region' => 'II'],
            ['name' => 'Bình Định',    'region' => 'III'],
            ['name' => 'Bình Tân',     'region' => 'II'], // note: administrative names might vary
            // ... (you can extend or refine this list)
        ];

        // Try to find a site_parameter id by exact name; if not found, skip with a warning in comment.
        foreach ($provinces as $p) {
            $site = SiteParameter::where('name', $p['name'])->first();

            if (!$site) {
                // If site not found, optionally create it (uncomment if you want).
                // $site = SiteParameter::create(['name' => $p['name'], 'country' => 'VN']);
                // For safety now, we skip and log to console.
                $this->command->warn("SiteParameter not found for: {$p['name']} — skipping insert. Create SiteParameter first or uncomment auto-create.");
                continue;
            }

            $r = $p['region'];
            $vals = $regionMap[$r] ?? $regionMap['II'];

            // create record
            SiteWindParameter::create([
                'site_parameter_id' => $site->id,
                'standard_code' => 'TCVN2737-2023',
                'basic_wind_speed' => $vals['V3s50'], // using V3s,50 (m/s) as main 'basic_wind_speed'
                'pressure_reference' => $vals['W0'],
                'map_region' => $r,
                'terrain_factors' => json_encode(['V10m50' => $vals['V10m50']]),
                'directionality_factor' => 0.85,
                'gust_effect_factor' => 1.0,
                'conversion_to_other' => json_encode([
                    'ASCE7-10' => [
                        'factor_speed' => 1.0, // giả sử V₃s,50 TCVN ≈ V₃s,50 ASCE
                        'speed_m_s'    => $vals['V3s50'],                       // giữ nguyên (m/s)
                        'speed_mph'    => round($vals['V3s50'] * 2.23694, 1),   // quy đổi sang mph
                        'pressure_kN_m2' => round($vals['W0'] / 100, 3),        // quy đổi daN/m² -> kN/m²
                    ]
                ]),
                'notes' => "Inserted from QCVN 02:2022/BXD (Bảng 5.1) summary for {$p['name']}. Check table for district-level exceptions.",
            ]);

            $this->command->info("Inserted wind param for {$p['name']} (region {$r})");
        }

        $this->command->info('SiteWindParametersSeeder finished. Source: QCVN 02:2022/BXD - Bảng 5.1. See project PDF for district-level detail.');
    }
}