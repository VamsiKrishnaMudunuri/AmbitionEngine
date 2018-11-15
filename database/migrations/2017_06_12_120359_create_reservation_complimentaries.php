<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationComplimentaries extends Migration
{

    private $table = 'reservation_complimentaries';
    private $reservationTable = 'reservations';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create($this->table, function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('version')->unsigned()->default(1);

            $table->bigInteger('reservation_id')->unsigned();
            $table->bigInteger('subscription_id')->unsigned();
            $table->bigInteger('subscription_complimentary_id')->unsigned();

            $table->decimal('credit', 12, 2)->default(0.00);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('subscription_id');
            $table->index('subscription_complimentary_id');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('reservation_id')->references('id')->on($this->reservationTable)->onDelete('cascade');


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
