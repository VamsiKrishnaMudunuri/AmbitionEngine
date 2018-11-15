<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyTable extends Migration
{
    private $table = 'properties';

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
            
            $table->boolean('status')->default(false);
            
            $table->string('name', 255)->default('');
            $table->string('slogan', 255)->default('');
            $table->string('timezone', 50)->default('');

            $table->string('official_email', 100)->default('');
            $table->string('support_email', 100)->default('');

            $table->string('office_phone_country_code', 6)->default('');
            $table->string('office_phone_area_code', 6)->default('');
            $table->string('office_phone_number', 20)->default('');
            $table->string('office_phone_extension', 6)->default('');
            $table->string('fax_country_code', 6)->default('');
            $table->string('fax_area_code', 6)->default('');
            $table->string('fax_number', 20)->default('');
            $table->string('fax_extension', 20)->default('');

            $table->string('city', 50)->default('');
            $table->string('state', 50)->default('');
            $table->string('postcode', 10)->default('');
            $table->string('country', 5)->default('');
            $table->string('address', 300)->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();
            
            $table->timestamps();

            $table->index('version');
            $table->index('company_id');
            $table->index('status');
            $table->index('name');
            $table->index('slogan');
            $table->index(['city', 'state', 'postcode', 'country'], $this->table . '_address_index');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');

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
