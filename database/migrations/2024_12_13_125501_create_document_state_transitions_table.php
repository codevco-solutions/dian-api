<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_state_transitions', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // Tipo de documento (invoice, order, etc.)
            $table->unsignedBigInteger('document_id');
            $table->foreignId('from_state_id')->constrained('document_states');
            $table->foreignId('to_state_id')->constrained('document_states');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->timestamp('transition_date');
            $table->boolean('requires_approval')->default(false);
            $table->timestamp('approval_date')->nullable();
            $table->string('approval_status')->nullable(); // approved, rejected
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas comunes
            $table->index(['document_type', 'document_id']);
            $table->index('user_id');
            $table->index('approver_id');
            $table->index('transition_date');
            $table->index('approval_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_state_transitions');
    }
};
