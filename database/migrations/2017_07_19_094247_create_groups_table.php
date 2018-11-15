<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    protected $connection;
    private $table = 'groups';

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
            $table->index('property_id');
            $table->index('status');
            $table->index(
                [
                    'name' => 'text',
                    'description' => 'text',
                    'category' => 'text',
                    'tags' => 'text'
                ],
                null,
                null,
                [
                    'weights' =>
                        [
                            'name' => 10,
                            'description' => 8,
                            'category' => 10,
                            'tags' => 10
                        ]
                ]
            );
            $table->index('offices');
            $table->index('stats.invites');
            $table->index('stats.joins');
            $table->index(['creator' => 1, 'editor' => -1]);
            $table->index(['deleted_at' => 1]);
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
