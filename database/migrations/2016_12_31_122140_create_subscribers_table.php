<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersTable extends Migration
{
    
    private $table = 'subscribers';
    private $parentTable = 'mailchimps';
    
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
            
            $table->integer('mailchimp_id')->unsigned();
            $table->integer('user_id')->unsigned()->default(0);
            
            $table->boolean('is_subscribe_from_mailchimp')->default(false);
            
            $table->string('email', 100)->default('');
            $table->string('language', 5)->default('');
            $table->string('full_name', 100)->default('');
            $table->string('first_name', 50)->default('');
            $table->string('last_name', 50)->default('');
            
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();
        
            $table->timestamps();
        
            $table->index('version');
            $table->index('user_id');
            $table->index('is_subscribe_from_mailchimp');
            $table->index('email');
            $table->index('language');
            $table->index(['full_name', 'first_name', 'last_name'], $this->table . '_name_index');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('mailchimp_id')->references('id')->on($this->parentTable)->onDelete('cascade');
        
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
