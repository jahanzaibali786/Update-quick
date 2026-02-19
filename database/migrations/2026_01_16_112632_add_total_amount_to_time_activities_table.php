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
        Schema::table('time_activities', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable()->default(0)->after('taxable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_activities', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
    }
};
