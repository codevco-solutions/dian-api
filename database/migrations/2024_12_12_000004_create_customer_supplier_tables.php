<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clientes
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('trade_name')->nullable();
            $table->foreignId('identification_type_id')->constrained();
            $table->string('identification_number');
            $table->string('verification_code')->nullable();
            $table->foreignId('organization_type_id')->constrained();
            $table->foreignId('tax_regime_id')->constrained();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();
            $table->string('address');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'identification_type_id', 'identification_number'], 'cust_unique');
        });

        // Responsabilidades tributarias del cliente
        Schema::create('customer_tax_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_responsibility_id')->constrained();
            $table->timestamps();

            $table->unique(['customer_id', 'tax_responsibility_id'], 'ctr_cust_tax_resp_unique');
        });

        // Contactos del cliente
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Proveedores
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('trade_name')->nullable();
            $table->foreignId('identification_type_id')->constrained();
            $table->string('identification_number');
            $table->string('verification_code')->nullable();
            $table->foreignId('organization_type_id')->constrained();
            $table->foreignId('tax_regime_id')->constrained();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();
            $table->string('address');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'identification_type_id', 'identification_number'], 'supp_unique');
        });

        // Responsabilidades tributarias del proveedor
        Schema::create('supplier_tax_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_responsibility_id')->constrained();
            $table->timestamps();

            $table->unique(['supplier_id', 'tax_responsibility_id'], 'str_supp_tax_resp_unique');
        });

        // Contactos del proveedor
        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_contacts');
        Schema::dropIfExists('supplier_tax_responsibilities');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customer_contacts');
        Schema::dropIfExists('customer_tax_responsibilities');
        Schema::dropIfExists('customers');
    }
};
