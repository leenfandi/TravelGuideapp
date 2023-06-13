<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_comments', function (Blueprint $table) {
            $table-> bigIncrements('id');
            $table->text('message');
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('activities_id')->nullable();
            $table->unsignedBigInteger('guide_id')->nullable();

           // this is working
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        $table->foreign('activities_id')  ->references('id')->on('activities') ->onDelete('cascade');

        $table->foreign('guide_id')->references('id')->on('guides')->onDelete('cascade');

            $table->timestamps();

        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_comments');
    }
};
