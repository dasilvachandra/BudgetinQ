<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengeluaran extends Model
{
	protected $table ="pengeluaran";
    public $timestamps = false;
    public function insert($data){
        // dd($data);
        DB::select('INSERT INTO pengeluaran (id_pengeluaran, nama_pengeluaran, jumlah,picture,id_jenis_pengeluaran) VALUES (?, ?, ?, ?, ?)', $data);
    }
    public function checkDuplicate($data){
        return DB::select('SELECT nama_pengeluaran, id_jenis_pengeluaran, waktu,jumlah 
        from transaksi inner join pengeluaran on jenis_transaksi=id_pengeluaran 
        where id=? and waktu = ? and nama_pengeluaran = ? and jumlah = ? and id_jenis_pengeluaran = ?', $data);
    }
    public function selectAll($range_date){
        return DB::table('pengeluaran')
        ->select(DB::raw('*, concat(group_category," [",jenis_pengeluaran,"]") as group_category'))
        ->join('transaksi','pengeluaran.id_pengeluaran','=','transaksi.jenis_transaksi')
        ->join('jenis_pengeluaran','jenis_pengeluaran.id_jenis_pengeluaran','=','pengeluaran.id_jenis_pengeluaran')
        ->join('group_category','jenis_pengeluaran.group_category_id','=','group_category.group_category_id')
        ->where('transaksi.id','=',Auth::user()->id)
        ->whereBetween('waktu', [$range_date['start_default'], $range_date['end_default']])
        ->get();
    }
    public function selectByID($id){
                
            $qSelectByID = "
            SELECT 
                users.id,
                transaksi.id_transaksi,
                -- concat(dayname(transaksi.waktu), ', ', DATE_FORMAT(transaksi.waktu, '%d/%m/%Y')) waktu,
                transaksi.waktu,
                pengeluaran.picture,
                pengeluaran.id_pengeluaran,
                pengeluaran.nama_pengeluaran,
                pengeluaran.jumlah,
                jenis_pengeluaran,
                id_jenis_pengeluaran,
                group_category_id,
                group_category
            FROM 
                transaksi inner join pengeluaran on transaksi.jenis_transaksi=pengeluaran.id_pengeluaran 
                inner join jenis_pengeluaran using(id_jenis_pengeluaran) inner join group_category using(group_category_id) inner join
                users on users.id=transaksi.id 
            WHERE 
                transaksi.jenis_transaksi = pengeluaran.id_pengeluaran and 
                users.id = transaksi.id and 
                users.email = ? and pengeluaran.id_pengeluaran = ?
                order by transaksi.waktu,transaksi.created_at asc ;";
        // echo $qSelectByID;
        $email=Auth::user()->email;
        $data = DB::select($qSelectByID,[$email,$id]);
        // dd($data);
        return $data;
    }
    public function selectByIDJPG($id){
                
        $qSelectByID = "
        SELECT 
            users.id,
            transaksi.id_transaksi,
            -- concat(dayname(transaksi.waktu), ', ', DATE_FORMAT(transaksi.waktu, '%d/%m/%Y')) waktu,
            transaksi.waktu,
            pengeluaran.picture,
            pengeluaran.id_pengeluaran,
            pengeluaran.nama_pengeluaran,
            pengeluaran.jumlah,
            jenis_pengeluaran,
            id_jenis_pengeluaran,
            group_category_id,
            group_category
        FROM 
            transaksi inner join pengeluaran on transaksi.jenis_transaksi=pengeluaran.id_pengeluaran 
            inner join jenis_pengeluaran using(id_jenis_pengeluaran) inner join group_category using(group_category_id) inner join
            users on users.id=transaksi.id 
        WHERE 
            transaksi.jenis_transaksi = pengeluaran.id_pengeluaran and 
            users.id = transaksi.id and 
            users.email = ? and pengeluaran.id_jenis_pengeluaran = ?
            order by transaksi.waktu,transaksi.created_at asc ;";
    // echo $qSelectByID;
    $email=Auth::user()->email;
    $data = DB::select($qSelectByID,[$email,$id]);
    // dd($data);
    return $data;
}

    public function totalDanaKeluar($start_default,$end_default)
    {
        // $start_default = $range_date['start_default'];
        // $end_default = $range_date['end_default'];
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

    public function totalDanaKeluarByKategori($start_default,$end_default,$id_jenis_pengeluaran)
    {
        // $start_default = $range_date['start_default'];
        // $end_default = $range_date['end_default'];
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $q='
        SELECT ifnull(sum(jumlah),0) as total
            FROM transaksi 
                inner join users using(id) 
                inner join pengeluaran on id_pengeluaran=jenis_transaksi 
            where id=? and email=? and waktu between ? and ? and id_jenis_pengeluaran = ? ;
        ';
        // dd($id_jenis_pengeluaran);
        return DB::select($q,[$id_user,$email,$start_default,$end_default,$id_jenis_pengeluaran])[0]->total;
    }
    
    // public function totalDanaKeluar($time)
    // {
    //     // dd($time);
    //     $id_user=Auth::user()->id;
    //     $email=Auth::user()->email;
    //     $q='
    //     SELECT ifnull(sum(jumlah),0) as total
    //         FROM transaksi 
    //             inner join users using(id) 
    //             inner join pengeluaran on id_pengeluaran=jenis_transaksi 
    //         where id=? and email=? and 
    //             waktu between ( select min(waktu) as waktu from transaksi where id=?) and ?;
    //     ';
    //     return DB::select($q,[$id_user,$email,$id_user,$time])[0]->total;
    // }

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

    public function remove($data){
        DB::select('DELETE pengeluaran, transaksi 
            from pengeluaran 
            inner join transaksi on id_pengeluaran = jenis_transaksi 
            inner join users using (id) 
            where id_pengeluaran = ? and id=? and email = ?;
            ', $data
        );
    }

    public function patch($data){
        DB::select('UPDATE pengeluaran set nama_pengeluaran = ?, jumlah = ? , picture = ?, id_jenis_pengeluaran = ? WHERE id_pengeluaran = ?', $data);
    }
    
    // public function selectRange($start_default,$end_default){
    //     $id=Auth::user()->id;
    //     $q=" SELECT 
    //     id_pengeluaran,
    //     DATE_FORMAT(waktu, '%d %M, %Y') waktu,
    //     nama_pengeluaran,
    //     jumlah ,
    //     jenis_pengeluaran,
    //     group_category_id,
    //     id_jenis_pengeluaran
    //     from transaksi inner join pengeluaran on jenis_transaksi=id_pengeluaran 
    //     inner join jenis_pengeluaran using(id_jenis_pengeluaran)
    //     where transaksi.id=? and waktu between ? and ? ;
    //     ";
    //     return DB::select($q,[$id,$start_default,$end_default]);
    // }
    public function selectRange($start_default,$end_default){
        return DB::table('pengeluaran')
        ->select(DB::raw('*, concat(group_category," [",jenis_pengeluaran,"]") as group_category'))
        ->join('transaksi','pengeluaran.id_pengeluaran','=','transaksi.jenis_transaksi')
        ->join('jenis_pengeluaran','jenis_pengeluaran.id_jenis_pengeluaran','=','pengeluaran.id_jenis_pengeluaran')
        ->join('group_category','jenis_pengeluaran.group_category_id','=','group_category.group_category_id')
        ->where([['transaksi.id','=',Auth::user()->id]])
        ->whereBetween('waktu', [$start_default, $end_default])
        ->get();
    }

    public function selectRangeByKategori($start_default,$end_default,$id_jenis_pengeluaran){
        return DB::table('pengeluaran')
        ->select(DB::raw('*, concat(group_category," [",jenis_pengeluaran,"]") as group_category'))
        ->join('transaksi','pengeluaran.id_pengeluaran','=','transaksi.jenis_transaksi')
        ->join('jenis_pengeluaran','jenis_pengeluaran.id_jenis_pengeluaran','=','pengeluaran.id_jenis_pengeluaran')
        ->join('group_category','jenis_pengeluaran.group_category_id','=','group_category.group_category_id')
        ->where([['transaksi.id','=',Auth::user()->id],['jenis_pengeluaran.id_jenis_pengeluaran','=',$id_jenis_pengeluaran]])
        ->whereBetween('waktu', [$start_default, $end_default])
        ->get();
    }
    public function GCPengeluaran(){
        return DB::select("select group_category_id, pengeluaran,gabung,pengeluaran, if(gabung=1,concat(group_category,' -> Synchronize'),concat(group_category,' -> Not Synchronize')) as group_category from group_category where pengeluaran=1");
    }
}
