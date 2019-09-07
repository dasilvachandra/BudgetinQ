<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendapatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pendapatan', function (Blueprint $table) {
            $table->string('id_pendapatan')->primary();
            $table->string('nama_pendapatan');
            $table->string('jumlah');
            $table->string('picture');
            $table->string('id_jenis_pendapatan');
            $table->foreign('id_jenis_pendapatan')->references('id_jenis_pendapatan')->on('jenis_pendapatan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pendapatan');
    }
}
