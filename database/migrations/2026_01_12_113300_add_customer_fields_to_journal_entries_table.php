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
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'customer_id')) {
                $table->unsignedInteger('customer_id')->nullable()->after('category');
            }
            if (!Schema::hasColumn('journal_entries', 'customer_name')) {
                $table->string('customer_name', 255)->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('journal_entries', 'customer_type')) {
                $table->string('customer_type', 50)->nullable()->after('customer_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'customer_name', 'customer_type']);
        });
    }
};
