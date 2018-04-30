<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserContentPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_user', function (Blueprint $table) {
            $table->integer('content_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title');
            $table->integer('admin')->unsigned()->default(0);
            $table->integer('notifications')->unsigned()->default(0);
            $table->integer('sorting')->unsigned()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_user');
    }
}
