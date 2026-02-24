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
        Schema::table('bills', function (Blueprint $table) {
            // Name fields
            if (!Schema::hasColumn('bills', 'payment_method')) {
                $table->string('payment_method',50)->nullable()->after('category_id');
            }
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
