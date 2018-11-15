<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityPricesTable extends Migration
{
    private $table = 'facility_prices';
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

            $table->boolean('is_taxable')->default(false);

            $table->decimal('strike_price', 12, 2)->default(0.00);
            $table->decimal('spot_price', 12, 2)->default(0.00);
            $table->decimal('member_price', 12, 2)->default(0.00);
            $table->decimal('deposit', 12, 2)->default(0.00);
            $table->integer('rule')->unsigned()->default(0);

            $table->text('complimentaries');

            $table->boolean('is_collect_deposit_offline')->default(false);
            $table->boolean('status')->default(false);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');

            $table->index('is_taxable');
            $table->index('rule');
            $table->index('is_collect_deposit_offline');
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
