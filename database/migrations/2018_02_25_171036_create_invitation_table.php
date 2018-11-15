<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    private $table = 'guests';

    public function up()
    {
        //
        Schema::create($this->table, function (Blueprint $table) {

            $table->increments('id');
            $table->integer('version')->unsigned()->default(1);

            $table->integer('property_id')->unsigned()->default(0);
            $table->integer('user_id')->unsigned()->default(0);

            $table->string('name', 255)->default('');
            $table->string('email', 255)->default('');
            $table->string('contact_no', 255)->default('');

            $table->timestamp('schedule')->nullable();

            $table->text('remark')->default('');
            $table->text('guest_list')->default('');

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('property_id');
            $table->index('user_id');
            $table->index('name');
            $table->index('email');
            $table->index('contact_no');
            $table->index(['creator', 'editor'], $this->table . '_publisher_index');
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
