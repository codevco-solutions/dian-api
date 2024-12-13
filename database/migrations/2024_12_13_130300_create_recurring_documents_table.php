<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recurring_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type');
            $table->unsignedBigInteger('document_id');
            $table->string('frequency_type');
            $table->integer('frequency_value');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamp('last_generated_at')->nullable();
            $table->date('next_generation_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('generation_day')->nullable();
            $table->json('generation_conditions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['document_type', 'document_id']);
            $table->index('is_active');
            $table->index('next_generation_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('recurring_documents');
    }
};
