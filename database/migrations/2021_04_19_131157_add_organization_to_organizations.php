<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Organization;

class AddOrganizationToOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Organization::insert([
            [
                'name' => 'CashTrip',
                'phone' => '79998888888',
                'email' => 'cashtrip@admin.com',
                'password' => '$2y$10$J0wP4hlrqvEvMUcPGvr.OOlHoeUjSXVgrigJUEpt7IK.q6x/WyKzy',
                'remember_token' => Str::random(60),
                'status' => '1',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Organization::where('email', 'cashtrip@admin.com')->delete();
    }
}
