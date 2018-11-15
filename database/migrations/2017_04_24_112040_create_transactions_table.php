<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{

    private $table = 'transactions';

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

            $table->integer('property_id')->unsigned()->nullable();
            $table->string('transaction_id', 100)->default('');
            $table->string('merchant_account_id', 255)->default('');

            $table->string('order_id', 100)->default('');
            $table->integer('type')->unsigned()->default(0);

            $table->string('presentment_currency',3)->default('');
            $table->decimal('presentment_amount', 12, 2)->default(0.000000);

            $table->string('settlement_currency',3)->default('');
            $table->decimal('settlement_exchange_rate', 12, 6)->default(0.000000);
            $table->decimal('settlement_amount', 12, 6)->default(0.000000);

            $table->string('status', 100)->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('property_id');
            $table->index('transaction_id');
            $table->index('merchant_account_id');
            $table->index('order_id');
            $table->index('type');
            $table->index('presentment_currency');
            $table->index('settlement_currency');
            $table->index('status');

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
