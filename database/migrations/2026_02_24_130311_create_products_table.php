<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Product_name', 999);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('sku', 50)->unique();
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('photo', 999)->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sell_price', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('reorder_point')->default(10);
            $table->decimal('wac', 10, 2)->default(0);
            $table->integer('stock_qty')->default(0);
            $table->string('Status', 50)->default('مفعل');
            $table->string('Created_by', 999);
            $table->timestamps();

            $table->index('sku');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
