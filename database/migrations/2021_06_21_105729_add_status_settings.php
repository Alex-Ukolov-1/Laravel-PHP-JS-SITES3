<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_types', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('departure_points', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('intermediate_points', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('stops_and_services', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('payment_types', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
        Schema::table('unit_types', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('cargo_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('departure_points', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('intermediate_points', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('stops_and_services', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
