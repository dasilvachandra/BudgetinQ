<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
	protected $table ="transaksi";
	// protected $fillable = ['nama_pengeluaran','jumlah'];
	// if(gabung = 1,concat('-',jumlah),jumlah) as
	public function insert($data){
		DB::select('INSERT INTO transaksi (id_transaksi, jenis_transaksi, waktu,created_at,updated_at,id) VALUES (?, ?, ?, ?, ?,?)', $data);
	}

    public function selectAllByGroup($id_user,$group_category){
    	 $q = "select * from 
				(
					(select 'Dana Keluar' as jenis_transaksi, waktu,nama_pengeluaran as nama_transaksi, concat('-',jumlah) as jumlah, group_category, group_category_id,id_jenis_pengeluaran as id_kategori, jenis_pengeluaran as kategori from pengeluaran inner join jenis_pengeluaran using(id_jenis_pengeluaran) inner join group_category using(group_category_id) inner join transaksi on jenis_transaksi=id_pengeluaran where transaksi.id=? and group_category=? ) 
					union all 
					(select 'Dana Masuk' as jenis_transaksi, waktu,nama_pendapatan as nama_transaksi, jumlah, group_category, group_category_id, id_jenis_pendapatan as id_kategori, jenis_pendapatan as kategori from pendapatan inner join jenis_pendapatan using(id_jenis_pendapatan) inner join group_category using(group_category_id) inner join transaksi on jenis_transaksi=id_pendapatan where transaksi.id=? and group_category=?)
				)
				as c";
		$id_user=Auth::user()->id;
		$data = DB::select($q,[$id_user,$group_category,$id_user,$group_category]);
		return $data;
	}
	public function patch($data){
        DB::select('UPDATE transaksi set waktu = ? WHERE jenis_transaksi = ? and id = ? ', $data);
	}
	
	public function sPeriode(){
		$id=Auth::user()->id;
		$q='select max(waktu) akhir, min(waktu) awal,  period_diff(date_format(max(waktu),"%Y%m"),date_format(min(waktu),"%Y%m")) as periode from transaksi where id=?';
		return DB::select($q,[$id]);
	}
}
