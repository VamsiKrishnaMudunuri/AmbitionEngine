<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityUnitsTable extends Migration
{
    private $table = 'facility_units';
    private $foreignTable = 'facilities';

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

            $table->integer('facility_id')->unsigned();

            $table->string('name', 255)->default('');
            $table->boolean('is_available')->default(false);
            $table->boolean('status')->default(false);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->unique(array('facility_id', 'name'));

            $table->index('version');
            $table->index('name');
            $table->index('is_available');
            $table->index('status');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('facility_id')->references('id')->on($this->foreignTable)->onDelete('cascade');

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
