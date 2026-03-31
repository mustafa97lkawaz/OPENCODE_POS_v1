<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('expire_date')->nullable()->after('barcode');
            $table->integer('alert_qty')->default(10)->after('expire_date');
            $table->boolean('is_variant')->default(false)->after('alert_qty');
            $table->string('variant_name', 100)->nullable()->after('is_variant');
            $table->string('unit', 50)->default('قطعة')->after('variant_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['expire_date', 'alert_qty', 'is_variant', 'variant_name', 'unit']);
        });
    }
}