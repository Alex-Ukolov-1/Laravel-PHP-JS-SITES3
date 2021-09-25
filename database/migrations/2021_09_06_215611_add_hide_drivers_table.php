<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHideDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('is_organization')->default(0);
        });

        $organizations = \App\Models\Organization::all();

        foreach ($organizations as $organization) {
            \App\Models\Driver::create([
                'organization_id' => $organization->id,
                'name' => 'Организация',
                'email' => $organization->email,
                'password' => \Illuminate\Support\Facades\Hash::make('driver'.$organization->id),
                'status' => true,
                'is_organization' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\Driver::where('is_organization', true)->delete();

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('is_organization');
        });
    }
}
