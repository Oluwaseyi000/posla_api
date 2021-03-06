<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('pid');
            $table->string('name');
            $table->string('username');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->uuid('country_id')->nullable();
            $table->string('gender')->nullable();
            $table->timestamp('dob')->nullable();
            $table->text('languages')->nullable()->comment("stringify all language into one");
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('users');
    }
}
