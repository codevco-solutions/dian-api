<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('document_type'); // Tipo de documento (invoice, order, etc.)
            $table->longText('content'); // Contenido de la plantilla
            $table->json('fields'); // Definición de campos
            $table->json('default_values')->nullable(); // Valores por defecto
            $table->json('validation_rules')->nullable(); // Reglas de validación
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas comunes
            $table->index('document_type');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_templates');
    }
};
