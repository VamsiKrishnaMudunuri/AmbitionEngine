<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalApiRequestsTable extends Migration
{

    private $table = 'external_api_requests';

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

            $table->string('name', 255)->default('');
            $table->string('path', 255)->default('');
            $table->string('code', 255)->default('');

            $table->text('headers');

            $table->timestamps();

            $table->index('version');

            $table->index('name');
            $table->index('path');
            $table->index('code');
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
