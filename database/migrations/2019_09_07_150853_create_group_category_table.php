<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_category', function (Blueprint $table) {
            $table->string('group_category_id')->primary();
            $table->string('group_category');
            $table->tinyInteger('pengeluaran');
            $table->tinyInteger('pendapatan');
            $table->tinyInteger('gabung');
            $table->text('note');
        });

        DB::table('group_category')->insert(
            [
                [
                    'group_category_id' => '1',
                    'group_category' => 'Kebutuhan',
                    'pengeluaran' => 1,
                    'pendapatan' => 0,
                    'gabung' => 0,
                    'note' => 'Transaksi segala kebutuhan untuk mempertahankan hidup serta untuk memperoleh kesejahteraan dan kenyamanan'
                ],
                [
                    'group_category_id' => '2',
                    'group_category' => 'Tabung',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Simpanan Dana'
                ],
                [
                    'group_category_id' => '3',
                    'group_category' => 'Investasi',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Transaksi Penanaman Modal'
                ],
                [
                    'group_category_id' => '4',
                    'group_category' => 'Utang',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Transaksi dana yang anda pinjam'
                ],
                [
                    'group_category_id' => '5',
                    'group_category' => 'Piutang',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Transaksi dana yang anda pinjamkan'
                ],
                [
                    'group_category_id' => '6',
                    'group_category' => 'Gaji',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Honor dan Upah'
                ],
                [
                    'group_category_id' => '7',
                    'group_category' => 'Usaha',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Transaksi keuangan untuk modal usaha'
                ],
                [
                    'group_category_id' => '8',
                    'group_category' => 'Piutang',
                    'pengeluaran' => 1,
                    'pendapatan' => 1,
                    'gabung' => 1,
                    'note' => 'Transaksi amal secara sukarela'
                ],

            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_category');
    }
}
