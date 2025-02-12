<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->datetime('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('type')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('locale')->nullable();
            $table->string('status')->nullable();
            $table->string('forgot_token')->nullable();
            $table->string('contact')->nullable();
            $table->boolean('is_approved')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
