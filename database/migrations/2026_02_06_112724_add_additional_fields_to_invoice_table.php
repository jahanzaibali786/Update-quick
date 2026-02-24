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
        Schema::table('invoices', function (Blueprint $table) {
            // Name fields
            if (!Schema::hasColumn('invoices', 'convert_type')) {
                $table->string('convert_type', 20)->nullable()->after('send_date');
            }
            if (!Schema::hasColumn('invoices', 'convert_id')) {
                $table->integer('convert_id')->nullable()->after('convert_type');
            }
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'convert_type')) {
                $table->dropColumn('convert_type');
            }
            if (Schema::hasColumn('invoices', 'convert_id')) {
                $table->dropColumn('convert_id');
            }
        });
    }
};
