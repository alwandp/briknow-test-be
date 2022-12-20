<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id');
            $table->foreignId('project_managers_id');
            $table->string('nama');
            $table->string('slug');
            $table->text('thumbnail')->nullable();
            $table->text('deskripsi');
            $table->text('metodologi')->nullable(); //sementara nullable
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('status_read')->nullable();
            $table->integer('status_finish')->length(5);
            $table->integer('is_recomended')->length(5);
            $table->integer('is_restricted')->length(1);
            $table->integer('flag_mcs')->length(1)->nullable();
            $table->integer('user_maker');
            $table->integer('user_checker');
            $table->integer('user_signer');
            $table->datetime('checker_at')->nullable();
            $table->datetime('signer_at')->nullable();
            $table->datetime('review_at')->nullable();
            $table->datetime('publish_at')->nullable();
            $table->text('r_note1')->nullable();
            $table->text('r_note2')->nullable();
            $table->tinyInteger('flag_es')->nullable();
            $table->timestamps();

            $table->engine = 'MyISAM';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
