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
        Schema::create('poliklinik_antrians', function (Blueprint $table) {
            $table->id();
            $table->string('kodePoli')->nullable();
            $table->string('namaPoli')->nullable();
            $table->string('kodeSubspesialis')->nullable();
            $table->string('namaSubspesialis')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('poliklinik_antrians');
    }
};
