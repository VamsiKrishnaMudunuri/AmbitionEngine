<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{

    private $table = 'currencies';

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

            $table->string('base',3)->default('');
            $table->string('quote',3)->default('');

            $table->decimal('base_amount', 12, 6)->default(0.000000);
            $table->decimal('quote_amount', 12, 6)->default(0.000000);

            $table->timestamps();

            $table->index('version');

            $table->index('base');
            $table->index('quote');
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
