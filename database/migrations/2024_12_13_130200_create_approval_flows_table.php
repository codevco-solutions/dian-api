<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('document_type');
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('document_type');
            $table->index('is_active');
        });

        Schema::create('approval_flow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('approver_type');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->integer('order');
            $table->boolean('is_required')->default(true);
            $table->integer('timeout_hours')->nullable();
            $table->string('on_timeout_action')->nullable();
            $table->json('conditions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['approval_flow_id', 'order']);
        });

        Schema::create('approval_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_id')->constrained();
            $table->string('document_type');
            $table->unsignedBigInteger('document_id');
            $table->string('status');
            $table->integer('current_step');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['document_type', 'document_id']);
            $table->index('status');
        });

        Schema::create('approval_instance_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_instance_id')->constrained()->onDelete('cascade');
            $table->foreignId('approval_flow_step_id')->constrained();
            $table->integer('order');
            $table->string('status');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('timeout_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['approval_instance_id', 'order']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approval_instance_steps');
        Schema::dropIfExists('approval_instances');
        Schema::dropIfExists('approval_flow_steps');
        Schema::dropIfExists('approval_flows');
    }
};
