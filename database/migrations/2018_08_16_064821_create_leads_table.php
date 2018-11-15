<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
	
	private $table = 'leads';
	private $userTable = 'users';
	private $propertyTable = 'properties';
	
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    Schema::create($this->table, function (Blueprint $table) {
		
		    $table->bigIncrements('id');
		    $table->integer('version')->unsigned()->default(1);
		
		    $table->bigInteger('parent_id')->unsigned()->nullable();
		    $table->string('path')->default('');
		    $table->integer('position')->default(0);
		    $table->integer('level')->default(0);
		
		    $table->boolean('is_editable')->default(false);
		    $table->string('ref', 100)->unique();
		    $table->integer('property_id')->unsigned();
		    $table->integer('referrer_id')->unsigned();
		    $table->integer('pic_id')->unsigned();
		    $table->integer('user_id')->unsigned();
		    $table->string('source', 20)->default('');
		    $table->string('commission_schema', 20)->default('');
		    $table->text('commission_reward')->default('');
		    $table->date('start_date')->nullable();
		    
		    $table->string('first_name', 100)->default('');
		    $table->string('last_name', 100)->default('');
		    $table->string('email', 100)->default('');
		    $table->string('company', 255)->default('');
		    $table->string('contact_country_code', 6)->default('');
		    $table->string('contact_area_code', 6)->default('');
		    $table->string('contact_number', 20)->default('');
		
		
		    $table->string('status', 20)->default('');
		    
		    $table->integer('creator')->unsigned()->nullable();
		    $table->integer('editor')->unsigned()->nullable();
		    $table->timestamps();
		
		    $table->index('version');
		
		    $table->index('position');
		    $table->index('level');
		
		
		    $table->index('is_editable');
		    $table->index('ref');
		    $table->index('property_id');
		    $table->index('referrer_id');
		    $table->index('pic_id');
		    $table->index('user_id');
		    $table->index('source');
		    $table->index('commission_schema');
		    
		    $table->index(['first_name', 'last_name'], $this->table . '_name_index');
		    $table->index('email');
		    $table->index('company');
		    
		    $table->index('status');
		    
		    $table->index(['creator', 'editor'], $this->table . '_publisher_index');
		    $table->index('created_at');
		    $table->index('updated_at');
		
		    $table->foreign('parent_id')->references('id')->on($this->table)->onDelete('cascade');
		    
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
