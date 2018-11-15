<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiosTable extends Migration
{

    protected $connection;
    private $table = 'bios';

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
            $table->unique('user_id');
            $table->index(
                [
                    'about' => 'text',
                    'skills' => 'text',
                    'interests' => 'text',
                    'services' => 'text',
                    'websites.name' => 'text',
                    'websites.url' => 'text'
                ],
                null,
                null,
                [
                 'weights' =>
                     [
                         'about' => 8,
                         'skills' => 10,
                         'interests' => 9,
                         'services' => 10,
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
