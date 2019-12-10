<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploaded_files', function (Blueprint $table) {
            /* @var $table Blueprint */
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->string('full_path')->nullable();
            $table->dateTime('process_date')->nullable();
            $table->integer('processed')->default(0);
            $table->integer('archived')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_files');
    }
}
