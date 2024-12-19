<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Resoluciones DIAN
        Schema::create('dian_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->string('type'); // factura_venta, nota_credito, nota_debito
            $table->string('resolution_number');
            $table->string('prefix')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->bigInteger('start_number');
            $table->bigInteger('end_number');
            $table->bigInteger('current_number');
            $table->boolean('is_active')->default(true);
            $table->text('technical_key')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'branch_id', 'type', 'resolution_number'], 'unique_resolution');
        });

        // Reglas de impuestos
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('type'); // iva, retencion_iva, retencion_fuente, retencion_ica
            $table->decimal('rate', 5, 2);
            $table->decimal('min_amount', 15, 2)->default(0); // Monto mínimo para aplicar
            $table->decimal('max_amount', 15, 2)->nullable(); // Monto máximo para aplicar
            $table->json('conditions')->nullable(); // Condiciones adicionales en JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Reglas de impuestos por región
        Schema::create('regional_tax_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rule_id')->constrained();
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->decimal('rate', 5, 2); // Tasa específica para la región
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tax_rule_id', 'country_id', 'state_id', 'city_id'], 'unique_regional_tax');
        });

        // Calendarios fiscales
        Schema::create('fiscal_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('type'); // mensual, bimestral, trimestral, cuatrimestral, semestral, anual
            $table->date('start_date');
            $table->date('end_date');
            $table->json('due_dates'); // Fechas límite para presentación y pago
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Configuración de numeración por tipo de documento
        Schema::create('document_numbering_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->string('document_type'); // orden_compra, cotizacion, remision, etc
            $table->string('prefix')->nullable();
            $table->integer('padding')->default(8); // Cantidad de dígitos para el número
            $table->bigInteger('last_number')->default(0);
            $table->string('format')->nullable(); // Formato personalizado: {PREFIX}-{YEAR}-{NUMBER}
            $table->boolean('reset_yearly')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'branch_id', 'document_type'], 'unique_numbering_config');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_numbering_configs');
        Schema::dropIfExists('fiscal_calendars');
        Schema::dropIfExists('regional_tax_rules');
        Schema::dropIfExists('tax_rules');
        Schema::dropIfExists('dian_resolutions');
    }
};
