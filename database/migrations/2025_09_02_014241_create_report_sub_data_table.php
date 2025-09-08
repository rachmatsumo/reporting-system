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
        Schema::create('report_sub_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('report_sub_design_id')->constrained('report_sub_designs')->onDelete('cascade');
            $table->json('data'); // Stores the actual field values
            $table->integer('row_index')->default(0); // For multiple instances of same sub-report
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['report_id', 'report_sub_design_id']);
            $table->index(['report_sub_design_id', 'row_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_sub_data');
    }
};