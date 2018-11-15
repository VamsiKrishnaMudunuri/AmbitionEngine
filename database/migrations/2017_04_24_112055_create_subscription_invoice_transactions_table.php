<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionInvoiceTransactionsTable extends Migration
{
    private $table = 'subscription_invoice_transactions';
    private $subscriptionInvoiceTable = 'subscription_invoices';


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

            $table->bigInteger('subscription_invoice_id')->unsigned();
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();

            $table->integer('type')->unsigned()->default(0);
            $table->integer('method')->unsigned()->default(0);
            $table->integer('mode')->unsigned()->default(0);
            $table->string('check_number', 255)->default('');
            $table->decimal('amount', 12, 2)->default(0.00);

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('status')->unsigned()->default(0);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('transaction_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('method');
            $table->index('mode');
            $table->index('check_number');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('subscription_invoice_id', $this->table . '_invoice_id_foreign')->references('id')->on($this->subscriptionInvoiceTable)->onDelete('cascade');


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
