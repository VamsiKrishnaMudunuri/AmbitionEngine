<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionInvoicesTable extends Migration
{
    private $table = 'subscription_invoices';
    private $subscriptionTable = 'subscriptions';


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

            $table->bigInteger('subscription_id')->unsigned();

            $table->string('ref', 100)->unique();
            $table->string('rec', 100)->nullable();

            $table->boolean('is_taxable')->default(false);
            $table->string('tax_name', 255)->default('');
            $table->integer('tax_value')->unsigned()->default(0);

            $table->string('currency',3)->default('');

            $table->integer('discount')->unsigned()->default(0);

            $table->decimal('price', 12, 2)->default(0.00);
            $table->decimal('deposit', 12, 2)->default(0.00);

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('new_end_date')->nullable();

            $table->integer('status')->unsigned()->default(0);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('ref');
            $table->index('rec');
            $table->index('is_taxable');
            $table->index('tax_name');
            $table->index('currency');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('new_end_date');
            $table->index('status');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('subscription_id')->references('id')->on($this->subscriptionTable)->onDelete('cascade');


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
