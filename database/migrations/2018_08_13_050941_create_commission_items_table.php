<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionItemsTable extends Migration
{
    private $table = 'commission_items';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {

            $table->increments('id');
            $table->integer('version')->unsigned()->default(1);
            $table->integer('commission_id')->unsigned();

            $table->double('percentage', 12)->default(0);
            $table->unsignedTinyInteger('type')->default(0);
            $table->unsignedTinyInteger('type_number')->nullable()->default(0);
            $table->double('min', 12)->nullable()->default(0);
            $table->double('max', 12)->nullable()->default(0);

            $table->integer('creator')->unsigned()->nullable();
            $table->integer('editor')->unsigned()->nullable();

            $table->timestamps();

            $table->index('version');
            $table->index('percentage');
            $table->index('type');
            $table->index('type_number');
            $table->index('min');
            $table->index('max');

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
        Schema::dropIfExists($this->table);
    }
}
