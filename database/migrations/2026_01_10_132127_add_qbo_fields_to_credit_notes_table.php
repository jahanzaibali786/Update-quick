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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('customer');
            $table->string('customer_email')->nullable()->after('customer_id');
            $table->date('issue_date')->nullable()->after('date');
            $table->integer('category_id')->nullable()->after('issue_date');
            $table->string('location_of_sale')->nullable()->after('category_id');
            $table->text('bill_to')->nullable()->after('location_of_sale');
            $table->integer('status')->default('2')->after('bill_to'); // Default to approved
            $table->decimal('subtotal', 15, 2)->nullable()->after('status');
            $table->decimal('taxable_subtotal', 15, 2)->nullable()->after('subtotal');
            $table->string('discount_type')->nullable()->after('taxable_subtotal');
            $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type');
            $table->decimal('total_discount', 15, 2)->nullable()->after('discount_value');
            $table->string('sales_tax_rate')->nullable()->after('total_discount');
            $table->decimal('total_tax', 15, 2)->nullable()->after('sales_tax_rate');
            $table->decimal('sales_tax_amount', 15, 2)->nullable()->after('total_tax');
            $table->decimal('total_amount', 15, 2)->nullable()->after('sales_tax_amount');
            $table->json('attachments')->nullable()->after('total_amount');
            $table->text('memo')->nullable()->after('attachments');
            $table->text('note')->nullable()->after('memo');
            $table->unsignedBigInteger('voucher_id')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn([
                'customer_id', 'customer_email', 'issue_date', 'category_id',
                'location_of_sale', 'bill_to', 'status', 'subtotal',
                'taxable_subtotal', 'discount_type', 'discount_value',
                'total_discount', 'sales_tax_rate', 'total_tax',
                'sales_tax_amount', 'total_amount', 'attachments',
                'memo', 'note', 'voucher_id'
            ]);
        });
    }
};
