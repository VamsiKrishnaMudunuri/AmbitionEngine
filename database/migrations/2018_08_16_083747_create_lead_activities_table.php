<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadActivitiesTable extends Migration
{
	
	private $table = 'lead_activities';
	private $leadTable = 'leads';
	
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
			
			$table->bigInteger('lead_id')->unsigned();
			
			$table->string('status', 20)->default('');
			
			$table->text('remark')->default('');
			
			$table->integer('creator')->unsigned()->nullable();
			$table->integer('editor')->unsigned()->nullable();
			$table->timestamps();
			
			$table->index('version');
			
			$table->index('status');
			
			$table->index(['creator', 'editor'], $this->table . '_publisher_index');
			$table->index('created_at');
			$table->index('updated_at');
			
			$table->foreign('lead_id')->references('id')->on($this->leadTable)->onDelete('cascade');
			
			
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
