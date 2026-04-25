<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('Expense_name');
            $table->string('payment_method', 10)->default('cash')->after('reference_number');
            $table->string('attachment')->nullable()->after('payment_method');
            $table->boolean('recurring')->default(false)->after('attachment');
            $table->string('recurring_type', 10)->nullable()->after('recurring');
            $table->string('status', 10)->default('paid')->after('recurring_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'reference_number',
                'payment_method',
                'attachment',
                'recurring',
                'recurring_type',
                'status',
            ]);
        });
    }
}
