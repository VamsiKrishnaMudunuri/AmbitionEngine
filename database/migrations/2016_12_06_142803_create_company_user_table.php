<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyUserTable extends Migration
{
    private $table = 'company_user';
    private $foreignTable = 'companies';
    private $otherTable = 'users';

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
            
            $table->integer('company_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->string('email', 100)->default('');
            $table->string('role', 20)->default('');
            $table->boolean('status')->default(false);
            $table->boolean('is_sent')->default(false);
            
            $table->string('designation', 100)->default('');
            $table->string('office_phone_country_code', 6)->default('');
            $table->string('office_phone_area_code', 6)->default('');
            $table->string('office_phone_number', 20)->default('');
            $table->string('office_phone_extension', 20)->default('');

            $table->string('fax_country_code', 6)->default('');
            $table->string('fax_area_code', 6)->default('');
            $table->string('fax_number', 20)->default('');
            $table->string('fax_extension', 20)->default('');
            
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();
            $table->index('version');
            $table->index('email');
            $table->index('role');
            $table->index('status');
            $table->index('is_sent');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');

            $table->foreign('company_id')->references('id')->on($this->foreignTable)->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on($this->otherTable)->onDelete('cascade');

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
