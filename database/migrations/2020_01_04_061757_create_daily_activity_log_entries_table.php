<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyActivityLogEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_activity_log_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->default(-1);
            $table->integer('contact_id')->default(-1);
            $table->string('action')->nullable();
            $table->string('family')->nullable();
            $table->string('occupation')->nullable();
            $table->string('recreation')->nullable();
            $table->string('dreams')->nullable();
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
        Schema::dropIfExists('daily_activity_log_entries');
    }
}
