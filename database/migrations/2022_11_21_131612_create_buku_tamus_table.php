<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buku_tamus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('organisasi')->nullable();
            $table->string('phone')->nullable();
            $table->string('alamat')->nullable();
            $table->string('tujuan')->nullable();
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
        Schema::dropIfExists('buku_tamus');
    }
};
