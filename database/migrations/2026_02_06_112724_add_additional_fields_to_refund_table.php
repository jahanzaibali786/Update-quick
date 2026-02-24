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
        Schema::table('refund_receipts', function (Blueprint $table) {
            // Name fields
            if (!Schema::hasColumn('refund_receipts', 'tax_id')) {
                $table->integer('tax_id')->nullable()->after('category_id');
            }
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('refund_receipts', 'tax_id')) {
                $table->dropColumn('tax_id');
            }
        });
    }
};
