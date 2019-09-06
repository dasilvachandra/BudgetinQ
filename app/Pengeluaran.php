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

}
