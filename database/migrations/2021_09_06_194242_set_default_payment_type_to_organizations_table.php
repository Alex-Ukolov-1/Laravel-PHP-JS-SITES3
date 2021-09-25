<?php

use App\Models\Settings\PaymentType;
use Illuminate\Database\Migrations\Migration;

class SetDefaultPaymentTypeToOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PaymentType::where('name', '=', 'Наличные')->update(['default' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PaymentType::where('name', '=', 'Наличные')->update(['default' => false]);
    }
}
