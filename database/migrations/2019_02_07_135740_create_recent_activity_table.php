<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecentActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recent_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('time')->default( DB::raw('CURRENT_TIMESTAMP') );
            $table->enum('event', ['note_view', 'course_view']);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('note_id')->nullable();
            $table->unsignedInteger('course_id')->nullable();
            $table->timestamps();

            $table->index('time');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('note_id')
                ->references('id')
                ->on('notes')
                ->onDelete('cascade');

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recent_activity');
    }
}
