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
        Schema::table('customers', function (Blueprint $table) {
            // Name fields
            if (!Schema::hasColumn('customers', 'title')) {
                $table->string('title', 16)->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('title');
            }
            if (!Schema::hasColumn('customers', 'middle_name')) {
                $table->string('middle_name', 100)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('customers', 'last_name')) {
                $table->string('last_name', 100)->nullable()->after('middle_name');
            }
            if (!Schema::hasColumn('customers', 'suffix')) {
                $table->string('suffix', 16)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('customers', 'company_name')) {
                $table->string('company_name')->nullable()->after('suffix');
            }
            if (!Schema::hasColumn('customers', 'print_on_check_name')) {
                $table->string('print_on_check_name', 110)->nullable()->after('company_name');
            }

            // Contact fields
            if (!Schema::hasColumn('customers', 'cc')) {
                $table->string('cc', 200)->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'bcc')) {
                $table->string('bcc', 200)->nullable()->after('cc');
            }
            if (!Schema::hasColumn('customers', 'mobile')) {
                $table->string('mobile', 30)->nullable()->after('contact');
            }
            if (!Schema::hasColumn('customers', 'fax')) {
                $table->string('fax', 30)->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('customers', 'other')) {
                $table->string('other', 30)->nullable()->after('fax');
            }
            if (!Schema::hasColumn('customers', 'website')) {
                $table->string('website', 1000)->nullable()->after('other');
            }

            // Sub-customer
            if (!Schema::hasColumn('customers', 'is_sub_customer')) {
                $table->boolean('is_sub_customer')->default(false)->after('website');
            }

            // Billing address additional field
            if (!Schema::hasColumn('customers', 'billing_address_2')) {
                $table->string('billing_address_2')->nullable()->after('billing_address');
            }

            // Shipping address additional field
            if (!Schema::hasColumn('customers', 'shipping_address_2')) {
                $table->string('shipping_address_2')->nullable()->after('shipping_address');
            }

            // Notes
            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable()->after('shipping_address_2');
            }

            // Payment fields
            if (!Schema::hasColumn('customers', 'primary_payment_method')) {
                $table->string('primary_payment_method')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('customers', 'terms')) {
                $table->string('terms')->nullable()->after('primary_payment_method');
            }
            if (!Schema::hasColumn('customers', 'delivery_method')) {
                $table->string('delivery_method')->nullable()->after('terms');
            }
            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->nullable()->after('delivery_method');
            }

            // Additional info fields
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->string('customer_type')->nullable()->after('credit_limit');
            }
            if (!Schema::hasColumn('customers', 'tax_exemption_details')) {
                $table->string('tax_exemption_details', 16)->nullable()->after('customer_type');
            }
            if (!Schema::hasColumn('customers', 'is_taxable')) {
                $table->boolean('is_taxable')->default(true)->after('tax_exemption_details');
            }
            if (!Schema::hasColumn('customers', 'default_tax_code')) {
                $table->string('default_tax_code')->nullable()->after('is_taxable');
            }
            if (!Schema::hasColumn('customers', 'opening_balance')) {
                $table->decimal('opening_balance', 15, 2)->nullable()->after('default_tax_code');
            }
            if (!Schema::hasColumn('customers', 'opening_balance_as_of')) {
                $table->date('opening_balance_as_of')->nullable()->after('opening_balance');
            }
            
            // Add owned_by if it doesn't exist
            if (!Schema::hasColumn('customers', 'owned_by')) {
                $table->integer('owned_by')->default(0)->after('created_by');
            }
            
            // Add type_id if it doesn't exist
            if (!Schema::hasColumn('customers', 'type_id')) {
                $table->integer('type_id')->nullable()->after('owned_by');
            }
            
            // Add qb_balance if it doesn't exist
            if (!Schema::hasColumn('customers', 'qb_balance')) {
                $table->decimal('qb_balance', 15, 2)->default(0.00)->after('type_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'title', 'first_name', 'middle_name', 'last_name', 'suffix', 
                'company_name', 'print_on_check_name', 'cc', 'bcc', 'mobile', 
                'fax', 'other', 'website', 'is_sub_customer', 'billing_address_2',
                'shipping_address_2', 'notes', 'primary_payment_method', 'terms',
                'delivery_method', 'credit_limit', 'customer_type', 
                'tax_exemption_details', 'is_taxable', 'default_tax_code',
                'opening_balance', 'opening_balance_as_of', 'owned_by', 
                'type_id', 'qb_balance'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
