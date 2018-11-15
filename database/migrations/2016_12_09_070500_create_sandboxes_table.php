<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSandboxesTable extends Migration
{

    private $table = 'sandboxes';

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


            $table->string('category', 50)->default('');

            $table->string('model', 50);
            $table->string('model_id', 32);
            
            $table->string('filename', 100)->default('');
            $table->string('mime_type', 150)->default('');
            $table->integer('size')->default(0);
            $table->string('title', 100)->default('');
            $table->longText('description')->nullable();
            $table->longText('attribute')->nullable();

            $table->string('url', 512)->default('');
            $table->string('image_url', 512)->default('');
            $table->string('video_url', 512)->default('');

            $table->integer('sort_order')->unsigned()->default(0);
    
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('category');
            $table->index('filename');
            $table->index(['model', 'model_id'], $this->table . '_model_index');
            $table->index('sort_order');
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
