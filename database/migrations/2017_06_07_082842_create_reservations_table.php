<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    private $table = 'reservations';
    private $userTable = 'users';
    private $propertyTable = 'properties';
    private $facilityTable = 'facilities';
    private $facilityUnitTable = 'facility_units';

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

            $table->integer('user_id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->integer('facility_id')->unsigned();
            $table->integer('facility_unit_id')->unsigned();
            $table->integer('seat')->unsigned()->default(1);

            $table->string('ref', 100)->unique();
            $table->string('rec', 100)->unique();


            $table->integer('rule')->unsigned()->default(0);
            $table->string('base_currency',3)->default('');
            $table->string('quote_currency',3)->default('');
            $table->decimal('base_rate', 12, 6)->default(0.000000);
            $table->decimal('quote_rate', 12, 6)->default(0.000000);
            $table->decimal('price', 12, 2)->default(0.00);
            $table->integer('discount')->unsigned()->default(0);

            $table->boolean('is_taxable')->default(false);
            $table->string('tax_name', 255)->default('');
            $table->integer('tax_value')->unsigned()->default(0);

            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('cancel_date');

            $table->text('remark')->default('');

            $table->integer('reminder')->unsigned()->default(0);
            $table->integer('status')->unsigned()->default(0);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('seat');
            $table->index('ref');
            $table->index('rec');
            $table->index('rule');
            $table->index('base_currency');
            $table->index('quote_currency');
            $table->index('is_taxable');
            $table->index('tax_name');
            $table->index('tax_value');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('cancel_date');
            $table->index('reminder');
            $table->index('status');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('user_id')->references('id')->on($this->userTable)->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on($this->propertyTable)->onDelete('cascade');
            $table->foreign('facility_id')->references('id')->on($this->facilityTable)->onDelete('cascade');
            $table->foreign('facility_unit_id')->references('id')->on($this->facilityUnitTable)->onDelete('cascade');

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
