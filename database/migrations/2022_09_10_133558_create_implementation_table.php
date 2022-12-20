<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImplementationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('implementation', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("slug");
            $table->foreignId('project_managers_id');
            $table->string("status");
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
            $table->string("thumbnail")->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('is_restricted')->default(0);
            $table->string('user_access')->nullable();
            $table->text('desc_piloting')->nullable();
            $table->text('desc_roll_out')->nullable();
            $table->text('desc_sosialisasi')->nullable();
            $table->text('project_id')->nullable();
            $table->integer('user_checker');
            $table->integer('user_signer');
            $table->integer('user_maker');
            $table->integer('updated_by')->nullable();
            $table->datetime('approve_at')->nullable();
            $table->integer('approve_by')->nullable();
            $table->datetime('publish_at')->nullable();
            $table->integer('publish_by')->nullable();
            $table->datetime('unpublish_at')->nullable();
            $table->integer('unpublish_by')->nullable();
            $table->datetime('reject_at')->nullable();
            $table->integer('reject_by')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('implementation');
    }
}
