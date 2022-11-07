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
        Schema::create('timings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('event')->nullable();
            $table->string('repeat')->nullable();
            $table->string('bounds')->nullable();
            $table->string('boundsDuration')->nullable();
            $table->string('boundsRange')->nullable();
            $table->string('boundsPeriod')->nullable();
            $table->string('count')->nullable();
            $table->string('countMax')->nullable();
            $table->string('duration')->nullable();
            $table->string('durationMax')->nullable();
            $table->string('durationUnit')->nullable();
            $table->string('frequency')->nullable();
            $table->string('frequencyMax')->nullable();
            $table->string('period')->nullable();
            $table->string('periodMax')->nullable();
            $table->string('periodUnit')->nullable();
            $table->string('dayOfWeek')->nullable();
            $table->string('timeOfDay')->nullable();
            $table->string('when')->nullable();
            $table->string('offset')->nullable();
            $table->string('code')->nullable();
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
        Schema::dropIfExists('timings');
    }
};
