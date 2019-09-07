<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJenisPengeluaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jenis_pengeluaran', function (Blueprint $table) {
            $table->string('id_jenis_pengeluaran')->primary();
            $table->string('jenis_pengeluaran');
            $table->string('color');
            $table->string('group_category_id');
            $table->foreign('group_category_id')->references('group_category_id')->on('group_category');
            $table->unsignedBigInteger('id');
            $table->foreign('id')->references('id')->on('users');
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
        Schema::dropIfExists('jenis_pengeluaran');
    }
}
