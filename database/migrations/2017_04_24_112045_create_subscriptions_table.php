<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{

    private $table = 'subscriptions';
    private $userTable = 'users';
    private $propertyTable = 'properties';
    private $packageTable = 'packages';
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
	
	        $table->bigInteger('lead_id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->integer('package_id')->unsigned()->nullable();
            $table->integer('facility_id')->unsigned()->nullable();
            $table->integer('facility_unit_id')->unsigned()->nullable();

            $table->boolean('is_package_promotion_code')->default(false);

            $table->integer('seat')->unsigned()->default(1);


            $table->string('ref', 100)->unique();

            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_taxable')->default(false);
            $table->string('tax_name', 255)->default('');
            $table->integer('tax_value')->unsigned()->default(0);

            $table->string('currency',3)->default('');
            $table->integer('discount')->unsigned()->default(0);
            $table->decimal('price', 12, 2)->default(0.00);
            $table->decimal('deposit', 12, 2)->default(0.00);

            $table->text('complimentaries');

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('contract_month')->unsigned()->default(1);
            $table->dateTime('billing_date')->nullable();
            $table->dateTime('next_billing_date')->nullable();
            $table->dateTime('next_reset_complimentaries_date')->nullable();

            $table->integer('is_auto_seat')->unsigned()->default(0);
            $table->integer('is_proceed_refund')->unsigned()->default(0);

            $table->integer('status')->unsigned()->default(0);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
	        $table->index('lead_id');
            $table->index('is_package_promotion_code');
            $table->index('seat');
            $table->index('ref');
            $table->index('is_recurring');
            $table->index('is_taxable');
            $table->index('tax_name');
            $table->index('currency');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('contract_month');
            $table->index('billing_date');
            $table->index('next_billing_date');
            $table->index('next_reset_complimentaries_date');
            $table->index('is_auto_seat');
            $table->index('is_proceed_refund');
            $table->index('status');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('property_id')->references('id')->on($this->propertyTable)->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on($this->packageTable)->onDelete('cascade');
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
