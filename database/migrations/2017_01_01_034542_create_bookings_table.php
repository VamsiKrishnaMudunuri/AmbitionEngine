<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    
    private $table = 'bookings';
    
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
	
	        $table->bigInteger('lead_id')->unsigned();
            $table->integer('property_id')->unsigned()->default(0);
            $table->string('name', 100)->default('');
            $table->string('email', 100)->default('');
            $table->string('company', 100)->default('');

            $table->string('contact_country_code', 6)->default('');
            $table->string('contact_area_code', 6)->default('');
            $table->string('contact_number', 20)->default('');

            $table->integer('pax')->unsigned()->default(0);
            $table->string('location', 255)->default('');
            $table->string('office', 255)->default('');

            $table->timestamp('schedule')->nullable();

            $table->string('request', 500)->default('');

            $table->boolean('type')->default(false);
            
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();
            
            $table->timestamps();
            
            $table->index('version');
	        $table->index('lead_id');
            $table->index('property_id');
            $table->index('name');
            $table->index('email');
            $table->index('company');
            $table->index('type');
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
