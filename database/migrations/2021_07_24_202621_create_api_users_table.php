<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiUsersTable extends Migration
{
    public function up()
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("api_key",60);
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_users');
    }
}
