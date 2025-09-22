<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Mở rộng projects: Thêm field preliminary_status và preliminary_meta (JSON cho gợi ý)
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('preliminary_status', ['none', 'draft', 'suggested', 'approved'])->default('none')->after('visibility');
            $table->json('preliminary_meta')->nullable()->after('settings'); // Lưu gợi ý sections, similarities
        });

        // Mở rộng models: Thêm flag is_preliminary
        Schema::table('models', function (Blueprint $table) {
            $table->boolean('is_preliminary')->default(false)->after('status');
        });

        // Thêm bảng project_parameters: Lưu thông số biên chi tiết, dễ query/search
        Schema::create('project_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('location')->nullable(); // Địa điểm (e.g., "Hanoi, VN" – dùng để tính wind/earthquake từ standards)
            $table->double('dead_load_roof')->default(0); // Tĩnh tải mái (kN/m2)
            $table->double('live_load_roof')->default(0); // Hoạt tải mái (kN/m2)
            $table->double('eave_height')->default(0); // Chiều cao mép mái (m)
            $table->integer('total_spans')->default(1); // Nhịp tổng (số nhịp)
            $table->double('max_span')->default(0); // Nhịp tối đa (m)
            $table->boolean('has_crane')->default(false); // Có cầu trục?
            $table->json('crane_details')->nullable(); // JSON: {"crane_weight": 10, "hoist_weight": 5, "mode": "A3", "count": 1}
            $table->json('extra_params')->nullable(); // Linh hoạt cho params khác (e.g., wind_speed, seismic_zone)
            $table->timestamps();
        });

        // Thêm bảng preliminary_suggestions (nếu cần log lịch sử gợi ý)
        Schema::create('preliminary_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('suggested_section_id')->nullable()->constrained('sections');
            $table->double('similarity_score')->default(0); // % tương đồng (tính từ logic)
            $table->json('meta')->nullable(); // Lý do gợi ý
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Reverse migrations...
    }
};
