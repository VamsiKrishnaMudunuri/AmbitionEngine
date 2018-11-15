<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBios extends Migration
{
    protected $connection;
    private $table = 'company_bios';

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
            $table->unique('company_id');
            $table->index(
                [
                    'about' => 'text',
                    'services' => 'text',
                    'skills' => 'text',
                    'websites.name' => 'text',
                    'websites.url' => 'text'
                ],
                null,
                null,
                [
                    'weights' =>
                        [
                            'about' => 8,
                            'services' => 10,
                            'skills' => 10,
                            'websites.name' => 7,
                            'websites.url' => 6
                        ]
                ]
            );
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
