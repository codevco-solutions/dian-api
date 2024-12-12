<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles ya se crean en la migración de permisos
        /*Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });*/

        // Empresas
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('commercial_name')->nullable();
            $table->foreignId('identification_type_id')->constrained();
            $table->string('identification_number')->unique();
            $table->string('verification_code')->nullable();
            $table->foreignId('organization_type_id')->constrained();
            $table->foreignId('tax_regime_id')->constrained();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('subdomain')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Responsabilidades tributarias de la empresa
        Schema::create('company_tax_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('tax_responsibility_id')->constrained();
            $table->timestamps();

            $table->unique(['company_id', 'tax_responsibility_id'], 'ctr_comp_tax_resp_unique');
        });

        // Sucursales
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('address');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('postal_code')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Usuarios
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained();
            $table->foreignId('company_id')->nullable()->after('role_id')->constrained();
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained();
            $table->string('identification_number')->nullable()->after('branch_id');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['role_id', 'company_id', 'branch_id', 'identification_number', 'phone', 'is_active']);
        });

        Schema::dropIfExists('branches');
        Schema::dropIfExists('company_tax_responsibilities');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('roles');
    }
};
