<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Document States
        if (!Schema::hasTable('document_states')) {
            Schema::create('document_states', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('type')->unique();
                $table->string('color')->nullable();
                $table->string('icon')->nullable();
                $table->integer('order')->default(0);
                $table->json('next_states')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('type');
                $table->index('order');
            });
        }

        // Document State Transitions
        if (!Schema::hasTable('document_state_transitions')) {
            Schema::create('document_state_transitions', function (Blueprint $table) {
                $table->id();
                $table->string('document_type');
                $table->unsignedBigInteger('document_id');
                $table->foreignId('from_state_id')->constrained('document_states');
                $table->foreignId('to_state_id')->constrained('document_states');
                $table->foreignId('user_id')->nullable()->constrained('users');
                $table->foreignId('approver_id')->nullable()->constrained('users');
                $table->text('comments')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['document_type', 'document_id']);
            });
        }

        // Document Templates
        if (!Schema::hasTable('document_templates')) {
            Schema::create('document_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('document_type');
                $table->json('content');
                $table->json('fields')->nullable();
                $table->json('validation_rules')->nullable();
                $table->boolean('is_default')->default(false);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('document_type');
                $table->index('is_default');
            });
        }

        // Document Attachments
        if (!Schema::hasTable('document_attachments')) {
            Schema::create('document_attachments', function (Blueprint $table) {
                $table->id();
                $table->string('document_type');
                $table->unsignedBigInteger('document_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('file_path');
                $table->string('file_type');
                $table->unsignedBigInteger('file_size');
                $table->foreignId('uploaded_by')->nullable()->constrained('users');
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['document_type', 'document_id']);
            });
        }

        // Approval Flows
        if (!Schema::hasTable('approval_flows')) {
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
        }

        // Approval Flow Steps
        if (!Schema::hasTable('approval_flow_steps')) {
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
        }

        // Approval Instances
        if (!Schema::hasTable('approval_instances')) {
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
        }

        // Approval Instance Steps
        if (!Schema::hasTable('approval_instance_steps')) {
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

        // Recurring Documents
        if (!Schema::hasTable('recurring_documents')) {
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
    }

    public function down()
    {
        Schema::dropIfExists('recurring_documents');
        Schema::dropIfExists('approval_instance_steps');
        Schema::dropIfExists('approval_instances');
        Schema::dropIfExists('approval_flow_steps');
        Schema::dropIfExists('approval_flows');
        Schema::dropIfExists('document_attachments');
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('document_state_transitions');
        Schema::dropIfExists('document_states');
    }
};
