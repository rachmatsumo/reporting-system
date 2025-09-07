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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female'])->nullable()->after('email');
            $table->string('photo')->nullable()->after('gender');
            $table->string('address')->nullable()->after('photo');
            $table->string('phone')->nullable()->after('address');
            $table->boolean('is_active')->default(1)->after('phone'); // tambahan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'photo', 'address', 'phone', 'is_active']);
        });
    }
};
