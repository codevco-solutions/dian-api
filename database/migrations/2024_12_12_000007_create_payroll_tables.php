<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Empleados
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('first_surname');
            $table->string('second_surname')->nullable();
            $table->foreignId('identification_type_id')->constrained();
            $table->string('identification_number');
            $table->date('birth_date');
            $table->string('gender'); // M, F
            $table->string('marital_status')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('postal_code')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'identification_type_id', 'identification_number'], 'emp_unique');
        });

        // Tipos de contrato
        Schema::create('contract_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de empleado
        Schema::create('employee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de jornada
        Schema::create('work_shift_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de riesgo laboral
        Schema::create('occupational_risk_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Código DIAN');
            $table->decimal('rate', 5, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Contratos
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('contract_type_id')->constrained();
            $table->foreignId('employee_type_id')->constrained();
            $table->foreignId('work_shift_type_id')->constrained();
            $table->foreignId('occupational_risk_type_id')->constrained();
            $table->string('number')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('base_salary', 12, 2);
            $table->string('currency_code');
            $table->decimal('transportation_allowance', 12, 2)->default(0);
            $table->integer('working_hours_week');
            $table->string('position');
            $table->string('area')->nullable();
            $table->text('notes')->nullable();
            $table->string('status'); // active, inactive, suspended, terminated
            $table->timestamps();
        });

        // Conceptos de nómina
        Schema::create('payroll_concepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type'); // earning, deduction
            $table->string('category'); // salary, transport, bonus, commission, overtime, health, pension, etc.
            $table->text('description')->nullable();
            $table->string('calculation_type'); // fixed, percentage, formula
            $table->decimal('value', 12, 2)->nullable();
            $table->string('formula')->nullable();
            $table->boolean('affects_social_security')->default(false);
            $table->boolean('affects_parafiscal')->default(false);
            $table->boolean('affects_retention')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        // Períodos de nómina
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('frequency'); // monthly, biweekly
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');
            $table->string('status'); // draft, processing, completed, cancelled
            $table->timestamps();

            $table->unique(['company_id', 'start_date', 'end_date']);
        });

        // Novedades de nómina
        Schema::create('payroll_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_concept_id')->constrained();
            $table->string('type'); // fixed, percentage, days, hours
            $table->decimal('value', 12, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Documentos de nómina electrónica
        Schema::create('payroll_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('contract_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();
            $table->string('type'); // individual, adjustment
            $table->string('number');
            $table->string('prefix')->nullable();
            $table->date('date');
            $table->string('currency_code');
            $table->decimal('exchange_rate', 10, 2)->default(1);
            $table->decimal('worked_days', 5, 2)->default(0);
            $table->decimal('worked_hours', 8, 2)->default(0);
            $table->decimal('earnings_total', 20, 2)->default(0);
            $table->decimal('deductions_total', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status'); // draft, sent, approved, rejected
            $table->string('uuid')->nullable(); // CUNE
            $table->json('qr_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'prefix', 'number']);
        });

        // Conceptos de nómina electrónica
        Schema::create('payroll_document_concepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_document_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_concept_id')->constrained();
            $table->string('type'); // earning, deduction
            $table->string('category');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('rate', 12, 2)->nullable();
            $table->decimal('amount', 20, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Horas extras y recargos
        Schema::create('payroll_document_overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_document_id')->constrained()->onDelete('cascade');
            $table->string('type'); // HED, HEN, HRN, HEDD, HEDN, HRDD, HRDN
            $table->decimal('hours', 8, 2);
            $table->decimal('rate', 12, 2);
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });

        // Seguridad social
        Schema::create('payroll_document_social_security', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_document_id')->constrained()->onDelete('cascade');
            $table->string('type'); // health, pension, arl
            $table->decimal('employee_rate', 5, 2);
            $table->decimal('employee_amount', 20, 2);
            $table->decimal('employer_rate', 5, 2);
            $table->decimal('employer_amount', 20, 2);
            $table->timestamps();
        });

        // Parafiscales
        Schema::create('payroll_document_parafiscal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_document_id')->constrained()->onDelete('cascade');
            $table->string('type'); // sena, icbf, ccf
            $table->decimal('rate', 5, 2);
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_document_parafiscal');
        Schema::dropIfExists('payroll_document_social_security');
        Schema::dropIfExists('payroll_document_overtimes');
        Schema::dropIfExists('payroll_document_concepts');
        Schema::dropIfExists('payroll_documents');
        Schema::dropIfExists('payroll_events');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('payroll_concepts');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('occupational_risk_types');
        Schema::dropIfExists('work_shift_types');
        Schema::dropIfExists('employee_types');
        Schema::dropIfExists('contract_types');
        Schema::dropIfExists('employees');
    }
};
