<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // Tipo de documento (invoice, order, etc.)
            $table->unsignedBigInteger('document_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas comunes
            $table->index(['document_type', 'document_id']);
            $table->index('uploaded_by');
            $table->index('file_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_attachments');
    }
};
