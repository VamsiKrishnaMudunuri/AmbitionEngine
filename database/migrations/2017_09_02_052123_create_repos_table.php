<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReposTable extends Migration
{

    private $table = 'repos';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create($this->table, function (Blueprint $table) {

            $table->increments('id');
            $table->integer('version')->unsigned()->default(1);

            $table->string('model', 50);
            $table->string('model_id', 32);

            $table->text('keywords')->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index(['model', 'model_id'], $this->table . '_model_index');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');

        });

        DB::statement(sprintf('ALTER TABLE %s ADD FULLTEXT INDEX %s_keywords_fulltext (keywords)', $this->table, $this->table));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        Schema::dropIfExists($this->table);
    }

}
