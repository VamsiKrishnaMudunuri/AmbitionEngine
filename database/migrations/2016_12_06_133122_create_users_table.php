<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    private $table = 'users';

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
            
            $table->string('email', 100)->unique();
            $table->string('username', 100)->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->string('role', 20)->default('');
            $table->boolean('status')->default(false);
            $table->string('currency',3)->default('');
            $table->string('timezone', 50)->default('');
            $table->string('language', 5)->default('');

            $table->string('salutation', 10)->default('');
		    $table->string('full_name', 100)->default('');
            $table->string('first_name', 100)->default('');
            $table->string('last_name', 100)->default('');
            $table->string('nric', 20)->default('');
            $table->string('passport_number', 20)->default('');
            $table->string('nationality', 50)->default('');
            $table->string('gender', 10)->default('');
            $table->date('birthday')->nullable();

            $table->string('phone_country_code', 6)->default('');
            $table->string('phone_area_code', 6)->default('');
            $table->string('phone_number', 20)->default('');
            $table->string('handphone_country_code', 6)->default('');
            $table->string('handphone_area_code', 6)->default('');
            $table->string('handphone_number', 20)->default('');

            $table->string('city', 50)->default('');
            $table->string('state', 50)->default('');
            $table->string('postcode', 10)->default('');
            $table->string('country', 5)->default('');
            $table->string('address1', 150)->default('');
            $table->string('address2', 150)->default('');

            $table->string('job', 100)->default('');
            $table->string('company', 255)->default('');
            $table->string('tag_number', 100)->default('');
            $table->string('focus_area', 100)->default('');

            $table->text('remark')->default('');
	
	        $table->string('tag_number', 100)->default('');
	
	        $table->text('focus_area')->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('username');
            $table->index('remember_token');
            $table->index('role');
            $table->index('status');
            $table->index('currency');
            $table->index('timezone');
            $table->index('language');
            $table->index(['full_name', 'first_name', 'last_name'], $this->table . '_name_index');
            $table->index(['city', 'state', 'postcode', 'country'], $this->table . '_address_index');
            $table->index('job');
            $table->index('company');
            $table->index('focus_area');
	        $table->index('tag_number');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');

        });

        DB::statement(sprintf('ALTER TABLE %s ADD FULLTEXT INDEX %s_username_fulltext (username)', $this->table, $this->table));
        DB::statement(sprintf('ALTER TABLE %s ADD FULLTEXT INDEX %s_name_fulltext (full_name, first_name, last_name, username, email)', $this->table, $this->table));
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
