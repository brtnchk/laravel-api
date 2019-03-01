<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NoteStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('note_id');
            $table->foreign('note_id')
                ->references('id')
                ->on('notes')
                ->onDelete('cascade');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedInteger('session_length')->nullable();
            $table->unsignedInteger('words_total')->nullable();
            $table->unsignedInteger('words_memorized')->nullable();
            $table->float('words_memorized_avg')->nullable();
            $table->unsignedInteger('words_memorized_max')->nullable();
            $table->float('memorization_min', 2, 1)->nullable()->unsigned();
            $table->float('memorization_max', 2, 1)->nullable()->unsigned();

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
        Schema::table('note_stats', function (Blueprint $table) {
            $table->dropForeign('note_stats_note_id_foreign');
            $table->dropForeign('note_stats_user_id_foreign');
        });

        Schema::dropIfExists('note_stats');
    }
}
