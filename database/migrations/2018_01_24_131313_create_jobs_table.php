<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    protected $connection;
    private $table = 'jobs';

    public function __construct() {
        $this->connection = Config::get('database.connections.mongodb.driver');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {

            $table->index('user_id');
            $table->index('company_id');
            $table->index('offices');
            $table->index('status');
            $table->index(
                [
                    'company_name' => 'text',
                    'company_industry' => 'text',
                    'company_description' => 'text',

                    'company_email' => 'text',
                    'company_phone_country_code' => 'text',
                    'company_phone_area_code' => 'text',
                    'company_phone_number' => 'text',

                    'company_city' => 'text',
                    'company_state' => 'text',
                    'company_postcode' => 'text',
                    'company_country' => 'text',
                    'company_address1' => 'text',
                    'company_address2' => 'text',

                    'job_title' => 'text',
                    'job_service' => 'text',
                    'job_description' => 'text',
                    'job_employment_type' => 'text',
                    'job_seniority_level' => 'text'

                ],
                'company_job',
                null,
                [

                    'weights' =>
                        [
                            'company_name' => 10,
                            'company_industry' => 10,
                            'company_description' => 8,

                            'company_email' => 10,
                            'company_phone_country_code' => 10,
                            'company_phone_area_code' => 10,
                            'company_phone_number' => 10,

                            'company_city' => 9,
                            'company_state' => 9,
                            'company_postcode' => 9,
                            'company_country' => 10,
                            'company_address1' => 9,
                            'company_address2' => 9,

                            'job_title' => 10,
                            'job_service' => 10,
                            'job_description' => 8,
                            'job_employment_type' => 9,
                            'job_seniority_level' => 9
                        ]
                ]
            );

            $table->index('stats.applies');
            $table->index('stats.employs');
            $table->index(['creator' => 1, 'editor' => -1]);
            $table->index(['created_at' => 1]);
            $table->index(['updated_at' => 1]);

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
        Schema::connection($this->connection)->drop($this->table);

    }
}
