<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorías de productos
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        // Marcas de productos
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        // Productos y servicios
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained('product_categories');
            $table->foreignId('brand_id')->nullable()->constrained('product_brands');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->comment('product, service');
            $table->foreignId('measurement_unit_id')->constrained();
            $table->decimal('base_price', 20, 2)->default(0);
            $table->decimal('sale_price', 20, 2)->default(0);
            $table->decimal('purchase_price', 20, 2)->default(0);
            $table->decimal('min_stock', 12, 4)->default(0);
            $table->decimal('max_stock', 12, 4)->default(0);
            $table->decimal('current_stock', 12, 4)->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            $table->unique(['company_id', 'barcode']);
        });

        // Impuestos de productos
        Schema::create('product_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained();
            $table->decimal('rate', 5, 2);
            $table->timestamps();

            $table->unique(['product_id', 'tax_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_taxes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_categories');
    }
};
