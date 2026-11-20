<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortcutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // the files-table
        Schema::create('shortcuts', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('UserID')->unsigned()->nullable();
            $table->string('Hash', 16)->unique();
            $table->string('Url', 1024);
            $table->string('Description', 1024)->nullable();
            $table->integer('Frequency')->default(0);
            $table->dateTime('CreationDate');
            $table->dateTime('UpdateDate');
            $table->dateTime('PublishDate')->nullable()->default(null);
            $table->dateTime('ExpirationDate')->nullable()->default(null);
            $table->dateTime('DeleteDate')->nullable()->default(null);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shortcuts');
    }
}
