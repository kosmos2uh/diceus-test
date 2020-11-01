<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_home_id')->unsigned()->index();
            $table->foreign('team_home_id')->references('id')->on('teams')->onDelete('restrict');
            $table->bigInteger('team_visitor_id')->unsigned()->index();
            $table->foreign('team_visitor_id')->references('id')->on('teams')->onDelete('restrict');
            $table->tinyInteger('goals_home')->nullable();
            $table->tinyInteger('goals_visitor')->nullable();
            $table->tinyInteger('finished')->default(0);
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
        Schema::dropIfExists('games');
    }
}
