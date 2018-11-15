<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilitiesTable extends Migration
{
    private $table = 'facilities';
    private $foreignTable = 'properties';


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

            $table->integer('property_id')->unsigned();

            $table->string('name', 255)->default('');
            $table->text('description');
            $table->text('facilities');
            $table->string('block', 20)->default('');
            $table->string('level', 20)->default('');
            $table->string('unit', 20)->default('');

            $table->integer('category')->unsigned()->default(0);
            $table->integer('quantity')->unsigned()->default(0);

            $table->integer('seat')->unsigned()->default(1);

            $table->integer('unit_running_number')->unsigned()->default(0);

            $table->text('business_hours');
            $table->boolean('status')->default(false);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('name');
            $table->index(['block', 'level', 'unit'], $this->table . '_building_index');
            $table->index('category');
            $table->index('quantity');
            $table->index('seat');
            $table->index('status');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('property_id')->references('id')->on($this->foreignTable)->onDelete('cascade');

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
