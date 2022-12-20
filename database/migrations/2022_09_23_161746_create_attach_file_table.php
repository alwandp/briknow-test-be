<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attach_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('com_id')->nullable();
            $table->foreignId('implementation_id')->nullable();
            $table->string('tipe');
            $table->string('nama');
            $table->string('jenis_file',10);
            $table->string('url_file');
            $table->string('size');
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
        Schema::dropIfExists('attach_file');
    }
}
