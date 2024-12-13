<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('composite_measurement_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_unit_id')->constrained('measurement_units');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('composite_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('composite_unit_id')->constrained('composite_measurement_units')->onDelete('cascade');
            $table->foreignId('from_unit_id')->constrained('measurement_units');
            $table->foreignId('to_unit_id')->constrained('measurement_units');
            $table->decimal('conversion_factor', 15, 6);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Usar un nombre más corto para el índice único
            $table->unique(['composite_unit_id', 'from_unit_id', 'to_unit_id'], 'unit_conversion_unique');
        });

        // Agregar soporte para unidades compuestas en productos
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('composite_unit_id')->nullable()->after('measurement_unit_id')
                ->constrained('composite_measurement_units')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['composite_unit_id']);
            $table->dropColumn('composite_unit_id');
        });
        Schema::dropIfExists('composite_unit_conversions');
        Schema::dropIfExists('composite_measurement_units');
    }
};
