<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntermediateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intermediate_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('format');
            $table->text('header');
            $table->text('line');
            $table->text('json');
            $table->integer('user_id')->default(-1);
            $table->integer('contact_csv_id')->default(-1);
            $table->integer('contact_id')->default(-1);
            $table->integer('finished')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intermediate_records');
    }
}
