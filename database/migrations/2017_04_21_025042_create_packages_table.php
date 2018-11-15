<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{

    private $table = 'packages';
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

            $table->boolean('is_taxable')->default(false);

            $table->decimal('strike_price', 12, 2)->default(0.00);
            $table->decimal('spot_price', 12, 2)->default(0.00);
            $table->decimal('deposit', 12, 2)->default(0.00);

            $table->text('complimentaries');

            $table->integer('type')->unsigned()->default(0);

            $table->boolean('status')->default(false);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');

            $table->index('name');

            $table->index('is_taxable');
            $table->index('type');
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
