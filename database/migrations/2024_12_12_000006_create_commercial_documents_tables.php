<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Referencias de documentos
        Schema::create('document_references', function (Blueprint $table) {
            $table->id();
            $table->morphs('referenceable');
            $table->string('type'); // invoice, credit_note, debit_note
            $table->string('number');
            $table->string('prefix')->nullable();
            $table->string('uuid')->nullable();
            $table->date('date');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // Cargos y descuentos
        Schema::create('allowance_charges', function (Blueprint $table) {
            $table->id();
            $table->morphs('chargeable');
            $table->string('type'); // allowance, charge
            $table->string('code');
            $table->text('reason');
            $table->decimal('base_amount', 20, 2);
            $table->decimal('rate', 5, 2);
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });

        // Retenciones
        Schema::create('tax_withholdings', function (Blueprint $table) {
            $table->id();
            $table->morphs('withholdable');
            $table->foreignId('tax_id')->constrained();
            $table->decimal('taxable_amount', 20, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 20, 2);
            $table->timestamps();
        });

        // Cotizaciones
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->string('number');
            $table->date('date');
            $table->date('expiration_date');
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, sent, approved, rejected, expired
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'number']);
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->foreignId('measurement_unit_id')->constrained();
            $table->decimal('unit_price', 20, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Órdenes
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->morphs('partner'); // customer o supplier
            $table->string('type'); // purchase, sale
            $table->string('number');
            $table->foreignId('quote_id')->nullable()->constrained();
            $table->date('date');
            $table->date('delivery_date')->nullable();
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, confirmed, partial, completed, cancelled
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'type', 'number']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('quote_item_id')->nullable()->constrained();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('delivered_quantity', 12, 4)->default(0);
            $table->foreignId('measurement_unit_id')->constrained();
            $table->decimal('unit_price', 20, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Facturas
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('type'); // standard, export, contingency
            $table->string('number');
            $table->string('prefix')->nullable();
            $table->date('date');
            $table->date('due_date');
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->decimal('paid_amount', 20, 2)->default(0);
            $table->decimal('pending_amount', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, sent, approved, rejected
            $table->string('payment_method');
            $table->string('payment_means');
            $table->integer('payment_term_days')->default(0);
            $table->string('uuid')->nullable(); // CUFE
            $table->json('qr_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'prefix', 'number']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->foreignId('measurement_unit_id')->constrained();
            $table->decimal('unit_price', 20, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_item_id')->nullable()->constrained();
            $table->foreignId('tax_id')->constrained();
            $table->decimal('taxable_amount', 20, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 20, 2);
            $table->timestamps();
        });

        // Notas Crédito
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('invoice_id')->constrained();
            $table->string('number');
            $table->string('prefix')->nullable();
            $table->date('date');
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, sent, approved, rejected
            $table->string('uuid')->nullable(); // CUDE
            $table->json('qr_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'prefix', 'number']);
        });

        Schema::create('credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_item_id')->nullable()->constrained();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->foreignId('measurement_unit_id')->constrained();
            $table->decimal('unit_price', 20, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('credit_note_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('credit_note_item_id')->nullable()->constrained();
            $table->foreignId('tax_id')->constrained();
            $table->decimal('taxable_amount', 20, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 20, 2);
            $table->timestamps();
        });

        // Notas Débito
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('invoice_id')->constrained();
            $table->string('number');
            $table->string('prefix')->nullable();
            $table->date('date');
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, sent, accepted, rejected
            $table->string('uuid')->nullable();
            $table->text('qr_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'number']);
        });

        Schema::create('debit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->foreignId('measurement_unit_id')->constrained();
            $table->decimal('unit_price', 20, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('debit_note_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_note_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained();
            $table->decimal('taxable_amount', 20, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 20, 2);
            $table->timestamps();
        });

        // Recibos de pago
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->string('number');
            $table->date('date');
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('total', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, completed, cancelled
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'number']);
        });

        Schema::create('payment_receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained();
            $table->decimal('amount', 20, 2);
            $table->string('payment_method');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipt_details');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('debit_note_taxes');
        Schema::dropIfExists('debit_note_items');
        Schema::dropIfExists('debit_notes');
        Schema::dropIfExists('credit_note_taxes');
        Schema::dropIfExists('credit_note_items');
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('invoice_taxes');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('tax_withholdings');
        Schema::dropIfExists('allowance_charges');
        Schema::dropIfExists('document_references');
    }
};
