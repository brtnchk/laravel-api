<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoteItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('note_items')) {
            Schema::create('note_items', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('position');
                $table->text('term_text')->nullable();
                $table->string('term_image')->nullable();
                $table->text('term_definition')->nullable();
                $table->text('passage_text')->nullable();

                $table->unsignedInteger('note_id');
                $table->foreign('note_id')
                    ->references('id')
                    ->on('notes')
                    ->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_items');
    }
}
