<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagePricesTable extends Migration
{

    private $table = 'package_prices';


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
            $table->boolean('status')->unsigned()->default(1);


            $table->string('currency',3)->default('');
            $table->string('country',3)->default('');
            $table->decimal('strike_price', 12, 2)->default(0.00);
            $table->decimal('spot_price', 12, 2)->default(0.00);
            $table->decimal('starting_price', 12, 2)->default(0.00);
            $table->decimal('ending_price', 12, 2)->default(0.00);

            $table->integer('type')->unsigned()->default(0);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('status');
            $table->index('currency');
            $table->index('country');
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
