<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignupInvitationsTable extends Migration
{
    private $table = 'signup_invitations';

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

            $table->string('email');
            $table->string('name');
            $table->string('token');

            $table->timestamps();

            $table->index('email');
            $table->index('name');
            $table->index('token');

            $table->index('created_at');
            $table->index('updated_at');

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
