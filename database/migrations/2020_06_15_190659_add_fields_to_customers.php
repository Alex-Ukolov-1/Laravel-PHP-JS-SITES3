<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->smallInteger('counterparty_type_id')->unsigned()->nullable()->after('id');
            $table->string('inn')->nullable();
            $table->string('bik')->nullable();
            $table->string('checking_account')->nullable();
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('counterparty_type_id');
            $table->dropColumn('inn');
            $table->dropColumn('bik');
            $table->dropColumn('checking_account');
            $table->dropColumn('note');
        });
    }
}
