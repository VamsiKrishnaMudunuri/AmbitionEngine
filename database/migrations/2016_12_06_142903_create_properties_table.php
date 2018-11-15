<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    private $table = 'properties';
    private $foreignTable = 'companies';

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
            $table->integer('company_id')->unsigned();
            
            $table->boolean('status')->default(false);
            $table->boolean('coming_soon')->default(false);
            $table->boolean('newest_space_status')->default(false);
            $table->boolean('is_prime_property_status')->default(false);
            $table->boolean('site_visit_status')->default(false);
            
            $table->string('name', 255)->default('');
            $table->string('currency',3)->default('');
            $table->string('timezone', 50)->default('');

            $table->string('tax_register_number', 255)->default('');
            $table->string('tax_name', 255)->default('');
            $table->integer('tax_value')->unsigned()->default(0);

            $table->string('merchant_account_id', 255)->default('');

            $table->string('official_email', 100)->default('');
            $table->string('info_email', 100)->default('');
            $table->string('support_email', 100)->default('');

            $table->string('office_phone_country_code', 6)->default('');
            $table->string('office_phone_area_code', 6)->default('');
            $table->string('office_phone_number', 20)->default('');
            $table->string('office_phone_extension', 20)->default('');
            $table->string('fax_country_code', 6)->default('');
            $table->string('fax_area_code', 6)->default('');
            $table->string('fax_number', 20)->default('');
            $table->string('fax_extension', 20)->default('');

            $table->string('place', 255)->default('');
            $table->string('building', 255)->default('');

            $table->string('city', 50)->default('');
            $table->string('state', 50)->default('');
            $table->string('postcode', 10)->default('');
            $table->string('country', 5)->default('');
            $table->string('address1', 150)->default('');
            $table->string('address2', 150)->default('');

            $table->string('country_slug', 5)->default('');
            $table->string('state_slug', 50)->default('');

            $table->mediumText('body')->default('');
            $table->text('overview')->default('');
	
	        $table->text('lead_notification_emails')->default('');
	        
            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();
            
            $table->timestamps();

            $table->index('version');
            $table->index('status');
            $table->index('coming_soon');
            $table->index('newest_space_status');
            $table->index('is_prime_property_status');
            $table->index('site_visit_status');
            $table->index('name');
            $table->index('currency');
            $table->index('timezone');
            $table->index('tax_register_number');
            $table->index('tax_name');
            $table->index('merchant_account_id');
            $table->index('official_email');
            $table->index('info_email');
            $table->index('support_email');
            $table->index('place');
            $table->index('building');
            $table->index('country_slug');
            $table->index('state_slug');
            $table->index(['city', 'state', 'postcode', 'country'], $this->table . '_address_index');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('company_id')->references('id')->on($this->foreignTable)->onDelete('cascade');

        });

        DB::statement(sprintf('ALTER TABLE %s ADD FULLTEXT INDEX %s_location_fulltext (name, place, building, city, state, country, address1, address2)', $this->table, $this->table));

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
