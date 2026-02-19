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
        Schema::table('credit_note_products', function (Blueprint $table) {
            if (!Schema::hasColumn('credit_note_products', 'taxable')) {
                $table->integer('taxable')->default(0)->after('price');
            }
            if (!Schema::hasColumn('credit_note_products', 'item_tax_price')) {
                $table->decimal('item_tax_price', 15, 2)->default(0.00)->after('taxable');
            }
            if (!Schema::hasColumn('credit_note_products', 'item_tax_rate')) {
                $table->decimal('item_tax_rate', 15, 2)->default(0.00)->after('item_tax_price');
            }
            if (!Schema::hasColumn('credit_note_products', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0.00)->after('item_tax_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_note_products', function (Blueprint $table) {
            $table->dropColumn(['taxable', 'item_tax_price', 'item_tax_rate', 'amount']);
        });
    }
};
