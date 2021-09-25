<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldInCounterpartyContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counterparty_contacts', function (Blueprint $table) {
            $table->renameColumn('user_id', 'counterparty_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counterparty_contacts', function (Blueprint $table) {
            $table->renameColumn('counterparty_id', 'user_id');
        });
    }
}
