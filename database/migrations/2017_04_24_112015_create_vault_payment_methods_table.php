<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVaultPaymentMethodsTable extends Migration
{

    private $table = 'vault_payment_methods';
    private $foreignTable = 'vaults';

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

            $table->integer('vault_id')->unsigned();
            $table->string('token', 50)->default('');
            $table->string('unique_number_identifier', 50)->default('');
            $table->string('card_number', 50)->default('');
            $table->string('expiry_date', 11)->default('');

            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(false);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('token');
            $table->index('unique_number_identifier');
            $table->index('card_number');
            $table->index('is_default');
            $table->index('status');

            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('vault_id')->references('id')->on($this->foreignTable)->onDelete('cascade');


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
