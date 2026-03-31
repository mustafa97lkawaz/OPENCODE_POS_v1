<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('company_name', 999)->nullable()->after('Supplier_name');
            $table->string('contact_person', 255)->nullable()->after('company_name');
            $table->decimal('balance', 10, 2)->default(0)->after('contact_person');
            $table->text('notes')->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'contact_person', 'balance', 'notes']);
        });
    }
}