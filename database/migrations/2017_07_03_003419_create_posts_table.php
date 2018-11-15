<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    protected $connection;
    private $table = 'posts';

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
            $table->index('group_id');
            $table->index('type');
            $table->index('start');
            $table->index('end');
            $table->index('registration_closing_date');
            $table->index('timezone');
            $table->index('status');
            $table->index(
                [
                    'name' => 'text',
                    'message' => 'text',
                    'category' => 'text',
                    'tags' => 'text'
                ],
                null,
                null,
                [
                    'weights' =>
                        [
                            'name' => 10,
                            'message' => 8,
                            'category' => 10,
                            'tags' => 10
                        ]
                ]
            );
            $table->index('mentions');
            $table->index('offices');
            $table->index('stats.likes');
            $table->index('stats.comments');
            $table->index('stats.goings');
            $table->index('stats.invites');
            $table->index('is_posted_from_admin');
            $table->index('has_quantity');
            $table->index('quantity');
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
