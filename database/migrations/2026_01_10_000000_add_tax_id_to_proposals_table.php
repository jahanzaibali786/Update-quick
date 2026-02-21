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
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'tax_id')) {
                $table->integer('tax_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('proposals', 'accepted_by')) {
                $table->string('accepted_by', 255)->nullable()->after('status');
            }
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (Schema::hasColumn('proposals', 'tax_id')) {
                $table->dropColumn('tax_id');
            }
            if (Schema::hasColumn('proposals', 'accepted_by')) {
                $table->dropColumn('accepted_by');
            }
        });
    }
};
