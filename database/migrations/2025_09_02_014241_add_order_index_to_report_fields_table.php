<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('report_fields', function (Blueprint $table) {
            // Add order_index if it doesn't exist
            if (!Schema::hasColumn('report_fields', 'order_index')) {
                $table->integer('order_index')->default(0)->after('order');
                $table->index(['report_design_id', 'order_index']);
            }
            
            // Add options column for select field types if it doesn't exist
            if (!Schema::hasColumn('report_fields', 'options')) {
                $table->json('options')->nullable()->after('default_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_fields', function (Blueprint $table) {
            if (Schema::hasColumn('report_fields', 'order_index')) {
                $table->dropIndex(['report_design_id', 'order_index']);
                $table->dropColumn('order_index');
            }
            
            if (Schema::hasColumn('report_fields', 'options')) {
                $table->dropColumn('options');
            }
        });
    }
};