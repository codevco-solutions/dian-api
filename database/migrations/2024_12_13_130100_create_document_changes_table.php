<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_changes', function (Blueprint $table) {
            $table->id();
            $table->string('changeable_type');
            $table->unsignedBigInteger('changeable_id');
            $table->string('action');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->json('changes')->nullable();
            $table->json('data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['changeable_type', 'changeable_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_changes');
    }
};
