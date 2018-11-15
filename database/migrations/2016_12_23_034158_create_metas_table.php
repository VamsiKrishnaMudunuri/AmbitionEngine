<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetasTable extends Migration
{
    private $table = 'metas';
    
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
    
            $table->string('slug', 255)->unique();
            $table->string('prefix_slug', 255)->default('');
            $table->string('country', 5)->default('');
            $table->string('keywords', 255)->default('');
            $table->string('description', 255)->default('');
    
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();
            
            $table->timestamps();
            
            $table->index('version');
            $table->index('prefix_slug');
            $table->index('country');
            $table->index(['model', 'model_id'], $this->table . '_model_index');
            $table->index('keywords');
            $table->index('description');
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
