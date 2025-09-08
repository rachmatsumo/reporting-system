<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_design_id')->constrained()->onDelete('cascade');
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
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_fields');
    }
}