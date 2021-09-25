<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProfitability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profitability', function (Blueprint $table) {
            $table->decimal('shipping_cost', 15, 2)->nullable();
            $table->decimal('profit', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profitability', function (Blueprint $table) {
            $table->dropColumn('profit');
            $table->dropColumn('shipping_cost');
        });
    }
}
