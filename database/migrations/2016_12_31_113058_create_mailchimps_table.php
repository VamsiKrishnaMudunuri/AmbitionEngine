<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailchimpsTable extends Migration
{
    private $table = 'mailchimps';

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
    
            $table->string('name', 255);
            $table->string('mailchimp_list_id', 255)->default('');
            $table->boolean('status')->default(0);
            $table->boolean('is_default')->default(false);
            $table->double('sort_order', 10, 2)->unsigned()->default(0.00);
    
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();
            
            $table->timestamps();
    
            $table->index('name');
            $table->index('mailchimp_list_id');
            $table->index('status');
            $table->index('is_default');
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
