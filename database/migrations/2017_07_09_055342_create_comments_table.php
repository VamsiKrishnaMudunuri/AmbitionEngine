<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    protected $connection;
    private $table = 'comments';

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

            $table->index('post_id');
            $table->index('user_id');
            $table->index('status');
            $table->index(
                [
                    'name' => 'text',
                    'message' => 'text',
                ],
                null,
                null,
                [
                    'weights' =>
                        [
                            'name' => 10,
                            'message' => 9,
                        ]
                ]
            );
            $table->index('mentions');
            $table->index('offices');
            $table->index('stats.likes');
            $table->index('stats.comments');
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

