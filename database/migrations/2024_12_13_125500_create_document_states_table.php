<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->nullable(); // Tipo de documento (invoice, order, etc.)
            $table->string('color', 7); // Formato hex #FFFFFF
            $table->string('icon', 50);
            $table->integer('order')->default(0);
            $table->boolean('is_system')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->boolean('allows_edit')->default(true);
            $table->boolean('allows_delete')->default(true);
            $table->json('next_states')->nullable(); // IDs de estados permitidos para transición
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_states');
    }
};
