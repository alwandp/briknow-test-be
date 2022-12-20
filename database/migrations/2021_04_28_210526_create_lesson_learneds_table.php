<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonLearnedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_learneds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId("divisi_id");
            $table->foreignId("consultant_id");
            $table->string("tahap");
            $table->text('lesson_learned');
            $table->text('detail');
            $table->datetime('checker_at')->nullable();
            $table->datetime('signer_at')->nullable();
            $table->datetime('review_at')->nullable();
            $table->datetime('publish_at')->nullable();
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
        Schema::dropIfExists('lesson_learneds');
    }
}
