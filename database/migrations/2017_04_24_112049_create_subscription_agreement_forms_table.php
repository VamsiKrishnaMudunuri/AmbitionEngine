<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionAgreementFormsTable extends Migration
{
    private $table = 'subscription_agreement_forms';
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

            $table->string('tenant_full_name', 100)->default('');
            $table->string('tenant_designation', 100)->default('');
            $table->string('tenant_nric', 30)->default('');
            $table->string('tenant_email', 100)->default('');
            $table->string('tenant_mobile', 100)->default('');
            $table->string('tenant_address', 300)->default('');
            $table->string('tenant_company_name', 255)->default('');
            $table->string('tenant_company_registration_number', 100)->default('');

            $table->string('landlord_company_name', 500)->default('');
            $table->string('landlord_company_email', 100)->default('');
            $table->string('landlord_company_account_name', 30)->default('');
            $table->string('landlord_company_account_number', 20)->default('');
            $table->string('landlord_company_bank_name', 30)->default('');
            $table->string('landlord_company_bank_switch_code', 30)->default('');
            $table->string('landlord_company_bank_address1', 150)->default('');
            $table->string('landlord_company_bank_address2', 150)->default('');

            $table->string('landlord_contact', 255)->default('');
            $table->string('landlord_full_name', 100)->default('');
            $table->string('landlord_designation', 100)->default('');

            $table->text('remark')->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
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
