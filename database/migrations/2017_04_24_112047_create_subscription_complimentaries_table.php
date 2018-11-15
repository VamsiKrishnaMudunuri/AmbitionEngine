<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionComplimentariesTable extends Migration
{
    private $table = 'subscription_complimentaries';
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

            $table->integer('category')->unsigned()->default(0);
            $table->decimal('credit', 12, 2)->default(0.00);
            $table->decimal('debit', 12, 2)->default(0.00);


            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('category');
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
