<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_change_requests', function (Blueprint $table) {
            $table->string('approved_by')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reservation_change_requests', function (Blueprint $table) {
            $table->dropColumn('approved_by');
        });
    }
};
