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
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->string('text_search');

            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('guide_id')->nullable();

           $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

           $table->foreign('region_id')  ->references('id')->on('regions') ->onDelete('cascade');

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
        Schema::dropIfExists('search_histories');
    }
};
