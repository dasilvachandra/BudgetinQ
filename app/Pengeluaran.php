<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengeluaran extends Model
{
	protected $table ="pengeluaran";
    public $timestamps = false;
    
    public function danakeluar($range_date)
    {
        $start_default = $range_date['start_default'];
        $end_default = $range_date['end_default'];
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $q='
        SELECT ifnull(sum(jumlah),0) as total
            FROM transaksi 
                inner join users using(id) 
                inner join pengeluaran on id_pengeluaran=jenis_transaksi 
            where id=? and email=? and waktu between ? and ?  ;
        ';
        return DB::select($q,[$id_user,$email,$start_default,$end_default])[0]->total;
    }

    public function totalDanaKeluar($time)
    {
        // dd($time);
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $q='
        SELECT ifnull(sum(jumlah),0) as total
            FROM transaksi 
                inner join users using(id) 
                inner join pengeluaran on id_pengeluaran=jenis_transaksi 
            where id=? and email=? and 
                waktu between ( select min(waktu) as waktu from transaksi where id=?) and ?;
        ';
        return DB::select($q,[$id_user,$email,$id_user,$time])[0]->total;
    }

    public function totalPerHari($time)
    {
        $id=Auth::user()->id;
        $q = " SELECT ifnull(sum(jumlah),0) as total from pengeluaran inner join transaksi on id_pengeluaran=jenis_transaksi where waktu =  ? and id=?";
        $result = DB::select($q,[$time,$id])[0]->total;
        return $result;
    }

    public function totalPerHariGroup($range_date)
    {
        $id=Auth::user()->id;
        $start_default = $range_date['start_default'];
        $end_default = $range_date['end_default'];
        $q = "SELECT waktu, ifnull(sum(jumlah),0) as total from pengeluaran inner join transaksi on id_pengeluaran=jenis_transaksi where waktu BETWEEN ? and ? and id=? group by waktu;";
        $result = DB::select($q,[$start_default,$end_default,$id]);
        return $result;
    }
}
