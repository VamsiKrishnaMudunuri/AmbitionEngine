<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCareerAppointmentsTable extends Migration
{
    private $table = 'career_appointments';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function(Blueprint $table) {
            $table->increments('id');
            $table->integer('version')->unsigned()->default(1);

            $table->unsignedInteger('career_id');
            $table->string('full_name', 100)->default('');
            $table->string('first_name', 100)->default('');
            $table->string('last_name', 100)->default('');
            $table->string('email', 100);
            $table->string('phone_country_code', 6)->default('');
            $table->string('phone_area_code', 6)->default('');
            $table->string('phone_number', 20)->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('email');
            $table->index('career_id');
            $table->index(['full_name', 'first_name', 'last_name'], $this->table . '_name_index');
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
        Schema::dropIfExists($this->table);
    }
}
