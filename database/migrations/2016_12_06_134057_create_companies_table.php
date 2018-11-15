<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    private $table = 'companies';

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

            $table->integer('user_id');

            $table->string('name', 255)->default('');
            $table->string('industry', 255)->default('');
            $table->string('headline', 100)->default('');
            $table->boolean('status')->default(false);
            $table->boolean('is_default')->default(false);

            $table->string('registration_number', 100)->default('');
            $table->string('type', 100)->default('');


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

            $table->string('city', 50)->default('');
            $table->string('state', 50)->default('');
            $table->string('postcode', 10)->default('');
            $table->string('country', 5)->default('');
            $table->string('address1', 150)->default('');
            $table->string('address2', 150)->default('');

            $table->string('account_name', 30)->default('');
            $table->string('account_number', 20)->default('');
            $table->string('bank_name', 30)->default('');
            $table->string('bank_switch_code', 30)->default('');
            $table->string('bank_address1', 150)->default('');
            $table->string('bank_address2', 150)->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('name');
            $table->index('industry');
            $table->index('headline');
            $table->index('status');
            $table->index('is_default');
            $table->index('registration_number');
            $table->index('type');
            $table->index('official_email');
            $table->index('info_email');
            $table->index('support_email');
            $table->index(['city', 'state', 'postcode', 'country'], $this->table . '_address_index');
            $table->index('account_name');
            $table->index('account_number');
            $table->index('bank_name');
            $table->index('bank_switch_code');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
            $table->index('created_at');
            $table->index('updated_at');

            DB::statement(sprintf('ALTER TABLE %s ADD FULLTEXT INDEX %s_location_fulltext (name, headline, city, state, country, address1, address2)', $this->table, $this->table));

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
