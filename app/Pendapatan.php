<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pendapatan extends Model
{
	protected $table ="pendapatan";
    public $timestamps = false;
    public function insert($data){
        // dd($data);
        DB::select('INSERT INTO pendapatan (id_pendapatan, nama_pendapatan, jumlah,picture,id_jenis_pendapatan) VALUES (?, ?, ?, ?, ?)', $data);
    }
    public function checkDuplicate($data){
        return DB::select('SELECT nama_pendapatan, id_jenis_pendapatan, waktu,jumlah 
        from transaksi inner join pendapatan on jenis_transaksi=id_pendapatan 
        where id=? and waktu = ? and nama_pendapatan = ? and jumlah = ? and id_jenis_pendapatan = ?', $data);
    }
    public function selectAll($range_date){
        return DB::table('pendapatan')
        ->select(DB::raw('*, concat(group_category," [",jenis_pendapatan,"]") as group_category'))
        ->join('transaksi','pendapatan.id_pendapatan','=','transaksi.jenis_transaksi')
        ->join('jenis_pendapatan','jenis_pendapatan.id_jenis_pendapatan','=','pendapatan.id_jenis_pendapatan')
        ->join('group_category','jenis_pendapatan.group_category_id','=','group_category.group_category_id')
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
                pendapatan.picture,
                pendapatan.id_pendapatan,
                pendapatan.nama_pendapatan,
                pendapatan.jumlah,
                jenis_pendapatan,
                id_jenis_pendapatan,
                group_category_id,
                group_category
            FROM 
                transaksi inner join pendapatan on transaksi.jenis_transaksi=pendapatan.id_pendapatan 
                inner join jenis_pendapatan using(id_jenis_pendapatan) inner join group_category using(group_category_id) inner join
                users on users.id=transaksi.id 
            WHERE 
                transaksi.jenis_transaksi = pendapatan.id_pendapatan and 
                users.id = transaksi.id and 
                users.email = ? and pendapatan.id_pendapatan = ?
                order by transaksi.waktu,transaksi.created_at asc ;";
        // echo $qSelectByID;
        $email=Auth::user()->email;
        $data = DB::select($qSelectByID,[$email,$id]);
        // dd($data);
        return $data;
    }
    public function selectByIDJPD($id){
             
            $qSelectByID = "
            SELECT 
                users.id,
                transaksi.id_transaksi,
                -- concat(dayname(transaksi.waktu), ', ', DATE_FORMAT(transaksi.waktu, '%d/%m/%Y')) waktu,
                transaksi.waktu,
                pendapatan.picture,
                pendapatan.id_pendapatan,
                pendapatan.nama_pendapatan,
                pendapatan.jumlah,
                jenis_pendapatan,
                id_jenis_pendapatan,
                group_category_id,
                group_category
            FROM 
                transaksi inner join pendapatan on transaksi.jenis_transaksi=pendapatan.id_pendapatan 
                inner join jenis_pendapatan using(id_jenis_pendapatan) inner join group_category using(group_category_id) inner join
                users on users.id=transaksi.id 
            WHERE 
                transaksi.jenis_transaksi = pendapatan.id_pendapatan and 
                users.id = transaksi.id and 
                users.email = ? and pendapatan.id_jenis_pendapatan = ?
                order by transaksi.waktu,transaksi.created_at asc ;";
        // echo $qSelectByID;
        $email=Auth::user()->email;
        $data = DB::select($qSelectByID,[$email,$id]);
        // dd($data);
        return $data;
    }
    public function GCPendapatan(){
        return DB::select("select group_category_id, pendapatan,gabung,pengeluaran, if(gabung=1,concat(group_category,' -> Synchronize'),concat(group_category,' -> Not Synchronize')) as group_category from group_category where pendapatan=1");
    }

    public function totalDanaMasuk($start_default,$end_default)
    {
        return DB::table('pendapatan')
        ->select(DB::raw('ifnull(sum(jumlah),0) as total'))
        ->join('transaksi','pendapatan.id_pendapatan','=','transaksi.jenis_transaksi')
        ->join('jenis_pendapatan','jenis_pendapatan.id_jenis_pendapatan','=','pendapatan.id_jenis_pendapatan')
        ->join('group_category','jenis_pendapatan.group_category_id','=','group_category.group_category_id')
        ->where([
            ['transaksi.id','=',Auth::user()->id],
            ['group_category.gabung','=','0']
        ])->whereBetween('waktu', [$start_default, $end_default])
        ->first()->total;
    }

    public function totalDanaMasukByKategori($start_default,$end_default,$id_jenis_pendapatan)
    {
        // $start_default = $range_date['start_default'];
        // $end_default = $range_date['end_default'];
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $q='
        SELECT ifnull(sum(jumlah),0) as total
            FROM transaksi 
                inner join users using(id) 
                inner join pendapatan on id_pendapatan=jenis_transaksi 
            where id=? and email=? and waktu between ? and ? and id_jenis_pendapatan = ? ;
        ';
        // dd($id_jenis_pendapatan);
        return DB::select($q,[$id_user,$email,$start_default,$end_default,$id_jenis_pendapatan])[0]->total;
    }
    
    // public function totalDanaMasuk($time)
    // {
    //     // dd($time);
    //     $id_user=Auth::user()->id;
    //     $email=Auth::user()->email;
    //     $q='
    //     SELECT ifnull(sum(jumlah),0) as total
    //         FROM transaksi 
    //             inner join users using(id) 
    //             inner join pendapatan on id_pendapatan=jenis_transaksi 
    //         where id=? and email=? and 
    //             waktu between ( select min(waktu) as waktu from transaksi where id=?) and ?;
    //     ';
    //     return DB::select($q,[$id_user,$email,$id_user,$time])[0]->total;
    // }

    public function totalPerHari($time)
    {
        $id=Auth::user()->id;
        $q = " SELECT ifnull(sum(jumlah),0) as total from pendapatan inner join transaksi on id_pendapatan=jenis_transaksi where waktu =  ? and id=?";
        $result = DB::select($q,[$time,$id])[0]->total;
        return $result;
    }

    public function totalPerHariGroup($range_date)
    {
        $id=Auth::user()->id;
        $start_default = $range_date['start_default'];
        $end_default = $range_date['end_default'];
        $q = "SELECT waktu, ifnull(sum(jumlah),0) as total from pendapatan inner join transaksi on id_pendapatan=jenis_transaksi where waktu BETWEEN ? and ? and id=? group by waktu;";
        $result = DB::select($q,[$start_default,$end_default,$id]);
        return $result;
    }

    public function remove($data){
        DB::select('DELETE pendapatan, transaksi 
            from pendapatan 
            inner join transaksi on id_pendapatan = jenis_transaksi 
            inner join users using (id) 
            where id_pendapatan = ? and id=? and email = ?;
            ', $data
        );
    }

    public function patch($data){
        DB::select('UPDATE pendapatan set nama_pendapatan = ?, jumlah = ? , picture = ?, id_jenis_pendapatan = ? WHERE id_pendapatan = ?', $data);
    }
    
    public function selectRange($start_default,$end_default){
        $id=Auth::user()->id;
        $q=" SELECT 
        id_pendapatan,
        DATE_FORMAT(waktu, '%d %M, %Y') waktu,
        nama_pendapatan,
        jumlah ,
        jenis_pendapatan,
        group_category_id,
        id_jenis_pendapatan
        from transaksi inner join pendapatan on jenis_transaksi=id_pendapatan 
        inner join jenis_pendapatan using(id_jenis_pendapatan)
        where transaksi.id=? and waktu between ? and ? ;
        ";
        return DB::select($q,[$id,$start_default,$end_default]);
    }

    public function selectRangeByKategori($start_default,$end_default,$id_jenis_pendapatan){
        return DB::table('pendapatan')
        ->select(DB::raw('*, concat(group_category," [",jenis_pendapatan,"]") as group_category'))
        ->join('transaksi','pendapatan.id_pendapatan','=','transaksi.jenis_transaksi')
        ->join('jenis_pendapatan','jenis_pendapatan.id_jenis_pendapatan','=','pendapatan.id_jenis_pendapatan')
        ->join('group_category','jenis_pendapatan.group_category_id','=','group_category.group_category_id')
        ->where([['transaksi.id','=',Auth::user()->id],['jenis_pendapatan.id_jenis_pendapatan','=',$id_jenis_pendapatan]])
        ->whereBetween('waktu', [$start_default, $end_default])
        ->get();
    }
}
