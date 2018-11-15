<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletTransactionsTable extends Migration
{
    private $table = 'wallet_transactions';
    private $foreignTable = 'wallets';


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

            $table->integer('wallet_id')->unsigned();
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->integer('reservation_id')->unsigned()->nullable();

            $table->string('rec', 100)->unique();

            $table->integer('type')->unsigned()->default(0);
            $table->integer('method')->unsigned()->default(0);
            $table->integer('mode')->unsigned()->default(0);
            $table->string('check_number', 255)->default('');

            $table->string('base_currency',3)->default('');
            $table->string('quote_currency',3)->default('');
            $table->decimal('base_amount', 12, 6)->default(0.000000);
            $table->decimal('quote_amount', 12, 6)->default(0.000000);
            $table->decimal('base_rate', 12, 6)->default(0.000000);
            $table->decimal('quote_rate', 12, 6)->default(0.000000);

            $table->boolean('status')->default(false);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('transaction_id');
            $table->index('reservation_id');
            $table->index('rec');
            $table->index('type');
            $table->index('method');
            $table->index('mode');
            $table->index('check_number');
            $table->index('base_currency');
            $table->index('quote_currency');
            $table->index('status');

            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('wallet_id')->references('id')->on($this->foreignTable)->onDelete('cascade');


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
