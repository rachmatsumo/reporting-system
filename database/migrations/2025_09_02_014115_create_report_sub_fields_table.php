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
        Schema::create('report_sub_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_sub_design_id')->constrained('report_sub_designs')->onDelete('cascade');
            $table->string('name');
            $table->string('label');
            $table->enum('type', [
                'text', 
                'textarea', 
                'textarea_rich', 
                'number', 
                'file', 
                'image', 
                'date', 
                'time', 
                'month', 
                'year', 
                'checkbox', 
                'select', 
                'map', 
                'personnel', 
                'attendance'
            ]);
            $table->boolean('required')->default(false);
            $table->text('default_value')->nullable();
            $table->json('options')->nullable(); // For select options, validation rules, etc.
            $table->integer('order_index')->default(0);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['report_sub_design_id', 'order_index']);
            $table->index(['name', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_sub_fields');
    }
};