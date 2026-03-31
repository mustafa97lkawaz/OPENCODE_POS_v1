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
            $table->enum('payment_method', ['cash', 'card', 'bank'])->default('cash')->after('reference_number');
            $table->string('attachment')->nullable()->after('payment_method');
            $table->boolean('recurring')->default(false)->after('attachment');
            $table->enum('recurring_type', ['daily', 'weekly', 'monthly'])->nullable()->after('recurring');
            $table->enum('status', ['paid', 'pending'])->default('paid')->after('recurring_type');
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
