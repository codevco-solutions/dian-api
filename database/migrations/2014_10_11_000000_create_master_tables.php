<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Países según ISO 3166-1
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code_2')->unique()->comment('ISO 3166-1 alpha-2');
            $table->string('code_3')->unique()->comment('ISO 3166-1 alpha-3');
            $table->string('numeric_code')->unique()->comment('ISO 3166-1 numeric');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Departamentos/Estados
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained();
            $table->string('name');
            $table->string('code')->comment('Código DANE');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_id', 'code']);
        });

        // Municipios/Ciudades
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained();
            $table->string('name');
            $table->string('code')->comment('Código DANE');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['state_id', 'code']);
        });

        // Monedas según ISO 4217
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('ISO 4217');
            $table->string('symbol');
            $table->integer('decimals')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de documentos de identidad
        Schema::create('identification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de organizaciones
        Schema::create('organization_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Regímenes tributarios
        Schema::create('tax_regimes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Responsabilidades tributarias
        Schema::create('tax_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de operación
        Schema::create('operation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->string('group')->nullable()->comment('Agrupación según DIAN');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de documento
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->string('prefix')->nullable()->comment('Prefijo por defecto');
            $table->text('description')->nullable();
            $table->string('group')->nullable()->comment('Agrupación según DIAN');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Medios de pago (Efectivo, crédito, etc.)
        Schema::create('payment_means', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->string('group')->nullable()->comment('Agrupación según DIAN');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Métodos de pago (Forma específica de pago)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->foreignId('payment_means_id')->nullable()->constrained()->comment('Medio de pago asociado');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Unidades de medida
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->string('un_cefact_code')->nullable()->comment('Código UN/CEFACT');
            $table->text('description')->nullable();
            $table->string('group')->nullable()->comment('Agrupación según UN/CEFACT');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tributos
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->decimal('default_rate', 5, 2)->nullable()->comment('Tarifa por defecto');
            $table->string('type')->comment('IVA, ReteFuente, etc.');
            $table->boolean('is_retention')->default(false)->comment('Indica si es una retención');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de referencia de precios
        Schema::create('reference_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de descuento
        Schema::create('discount_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de cargo
        Schema::create('charge_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de eventos
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->string('group')->nullable()->comment('Agrupación según DIAN');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_types');
        Schema::dropIfExists('charge_types');
        Schema::dropIfExists('discount_types');
        Schema::dropIfExists('reference_prices');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('measurement_units');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_means');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('operation_types');
        Schema::dropIfExists('tax_responsibilities');
        Schema::dropIfExists('tax_regimes');
        Schema::dropIfExists('organization_types');
        Schema::dropIfExists('identification_types');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};
