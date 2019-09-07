<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengeluaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->string('id_pengeluaran')->primary();
            $table->string('nama_pengeluaran');
            $table->string('jumlah');
            $table->string('picture');
            $table->string('id_jenis_pengeluaran');
            $table->foreign('id_jenis_pengeluaran')->references('id_jenis_pengeluaran')->on('jenis_pengeluaran');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengeluaran');
    }
}
