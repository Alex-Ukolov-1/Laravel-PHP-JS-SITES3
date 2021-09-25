<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('trip_id');
            $table->integer('trip_document_number');
            $table->string('document_path');
            $table->string('document_comment');
            $table->string('document_type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_documents');
    }
}
