<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('product_categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('product_categories')->onDelete('set null');
            }
            if (!Schema::hasColumn('product_categories', 'code')) {
                $table->string('code')->after('name')->nullable();
            }
            if (!Schema::hasColumn('product_categories', 'level')) {
                $table->integer('level')->default(1)->after('code');
            }
            if (!Schema::hasColumn('product_categories', 'path')) {
                $table->string('path')->after('level')->nullable();
            }
        });

        // Crear tabla para atributos de categoría
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // text, number, boolean, date, select
            $table->json('options')->nullable(); // Para tipos select
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Crear tabla para valores de atributos de productos
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('category_attributes')->onDelete('cascade');
            $table->text('value');
            $table->timestamps();

            $table->unique(['product_id', 'attribute_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('category_attributes');
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'code', 'level', 'path']);
        });
    }
};
