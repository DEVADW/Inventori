<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspeksi_monitor', function (Blueprint $table) {
            $table->string('status_approval')->default('menunggu')->after('diketahui_oleh');
            $table->string('approved_by')->nullable()->after('status_approval');
            $table->string('qr_code_persetujuan')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('qr_code_persetujuan');
        });
    }

    public function down(): void
    {
        Schema::table('inspeksi_monitor', function (Blueprint $table) {
            $table->dropColumn(['status_approval', 'approved_by', 'qr_code_persetujuan', 'approved_at']);
        });
    }
};
