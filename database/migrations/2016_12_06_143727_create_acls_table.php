<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclsTable extends Migration
{
    private $table = 'acls';

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
            $table->integer('model_id')->unsigned();

            $table->text('rights');
    
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('version');
            $table->index(['model', 'model_id'], $this->table . '_model_index');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');

        });

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
