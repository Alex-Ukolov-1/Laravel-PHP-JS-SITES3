<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin;

class AddAdminToAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Admin::insert([
            [
                'name' => 'admin',
                'phone' => '79998888888',
                'email' => 'admin@admin.com',
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
        Admin::where('email', 'admin@admin.com')->delete();
    }
}
