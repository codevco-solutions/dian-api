<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Secuencias de documentos
        Schema::create('doc_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('type'); // invoice, credit_note, debit_note, payroll
            $table->string('prefix')->nullable();
            $table->bigInteger('next_number');
            $table->integer('padding')->default(8);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'type', 'prefix']);
        });

        // Resoluciones DIAN
        Schema::create('doc_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('doc_sequence_id')->constrained();
            $table->string('resolution_number');
            $table->string('type'); // invoice, credit_note, debit_note, payroll
            $table->date('resolution_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('prefix')->nullable();
            $table->bigInteger('start_number');
            $table->bigInteger('end_number');
            $table->string('technical_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'resolution_number', 'type']);
        });

        // Certificados digitales
        Schema::create('digital_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('type'); // certificate, software
            $table->text('certificate')->nullable();
            $table->string('password')->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('pin')->nullable(); // PIN software DIAN
            $table->string('software_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Plantillas de documentos
        Schema::create('doc_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('type'); // invoice, credit_note, debit_note, payroll
            $table->string('name');
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->text('body')->nullable();
            $table->json('styles')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Logs de documentos
        Schema::create('doc_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('type'); // created, updated, sent, accepted, rejected
            $table->string('status');
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Logs DIAN
        Schema::create('dian_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('type'); // send, validate, status
            $table->string('status');
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->string('tracking_id')->nullable();
            $table->timestamps();
        });

        // Logs de errores
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('type');
            $table->string('code')->nullable();
            $table->text('message');
            $table->text('trace')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
        Schema::dropIfExists('dian_logs');
        Schema::dropIfExists('doc_logs');
        Schema::dropIfExists('doc_templates');
        Schema::dropIfExists('digital_certificates');
        Schema::dropIfExists('doc_resolutions');
        Schema::dropIfExists('doc_sequences');
    }
};
