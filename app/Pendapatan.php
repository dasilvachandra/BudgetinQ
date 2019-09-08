<?php

namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pendapatan extends Model
{
	protected $table ="pendapatan";
    public $timestamps = false;

    public function selectAll($range_date){
        
        $q=" SELECT 
        DATE_FORMAT(waktu, '%d %M, %Y') waktu,
        nama_pendapatan,
        jumlah ,
        jenis_pendapatan
        from transaksi inner join pendapatan on jenis_transaksi=id_pendapatan 
        inner join jenis_pendapatan using(id_jenis_pendapatan)
        where transaksi.id=? and waktu between ? and ? ;
        ";
        $id=Auth::user()->id;
        $start_default = $range_date['start_default'];
        $end_default = $range_date['end_default'];
        return DB::select($q,[$id,$start_default,$end_default]);
	}
    public function danamasuk($range_date)
    {
        $start_default = $range_date['start_default'];
        $end_default = $range_date['end_default'];
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $q='
        SELECT ifnull(sum(jumlah),0) as total
            FROM transaksi 
                inner join users using(id) 
                inner join pendapatan on id_pendapatan=jenis_transaksi 
            where id=? and email=? and waktu between ? and ?  ;
        ';
        return DB::select($q,[$id_user,$email,$start_default,$end_default])[0]->total;
    }

    public function totalDanaMasuk($time)
    {
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;

        $q='
            SELECT sum(jumlah) as total FROM transaksi 
                    inner join users using(id) 
                    inner join pendapatan on id_pendapatan=jenis_transaksi 
            where id=? and email=? and 
                    waktu between ( select min(waktu) as waktu from transaksi where id=?) and ?;
        ';
        return DB::select($q,[$id_user,$email,$id_user,$time])[0]->total;
    }

}
