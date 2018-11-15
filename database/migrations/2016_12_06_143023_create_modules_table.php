<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    private $table = 'modules';

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
            $table->string('controller', 255)->unique();

            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('path')->default('');
            $table->integer('position')->default(0);
            $table->integer('level')->default(0);

            $table->string('name', 100)->default('');
            $table->text('description');
            $table->string('icon', 50)->default('');
            $table->boolean('status')->default(false);
            $table->boolean('is_module')->default(false);

            $table->text('rights');
    
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('controller');

            $table->index('position');
            $table->index('level');
            $table->foreign('parent_id')->references('id')->on($this->table)->onDelete('cascade');

            $table->index('name');
            $table->index('status');
            $table->index('is_module');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');

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
