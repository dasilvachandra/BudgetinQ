<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JenisPendapatan extends Model
{
	protected $table ="jenis_pendapatan";
	public $timestamps = false;
	// protected $fillable = ['nama_pendapatan','jumlah'];

    private $qSelectByName="
        SELECT id_jenis_pendapatan,jenis_pendapatan,group_category_id,color,id
        FROM 
            jenis_pendapatan 
                left join pendapatan using(id_jenis_pendapatan) 
                inner join users using(id) 
        where users.id=? and email = ? and jenis_pendapatan = ?;
     ";
    private $qPengByKat = "
        SELECT * 
        FROM pendapatan 
        LEFT JOIN transaksi 
        ON jenis_transaksi = id_pendapatan 
        WHERE id=?
        AND id_jenis_pendapatan = ? ";

    private $qByDate = '
        SELECT ifnull((
            select sum(jumlah)
            from pendapatan inner join transaksi on jenis_transaksi = id_pendapatan 
            where id=? and waktu=?),"0") as jumlah
    ';
    public function selectAll(){
        $q="SELECT id_jenis_pendapatan, jenis_pendapatan, count(jumlah) as jt, sum(jumlah) as total, group_category, group_category_id from jenis_pendapatan inner join group_category using(group_category_id) inner join pendapatan using (id_jenis_pendapatan) inner join transaksi on id_pendapatan = jenis_transaksi where transaksi.id=? group by jenis_pendapatan;";
        $id=Auth::user()->id;
        return DB::select($q,[$id]);
    }
    
    public function selectRange($start_default,$end_default){
        $q="SELECT a.id_jenis_pendapatan, a.jenis_pendapatan, ifnull(jt,0) as jt, ifnull(total,0) as total, a.group_category, a.group_category_id 
        from (select id_jenis_pendapatan,jenis_pendapatan, group_category, group_category_id, id from jenis_pendapatan inner join group_category using(group_category_id) where id=?) as a left join (
            SELECT id_jenis_pendapatan, jenis_pendapatan, count(jumlah) as jt, sum(jumlah) as total, group_category, group_category_id 
            from jenis_pendapatan 
            inner join group_category using(group_category_id) 
            inner join pendapatan using (id_jenis_pendapatan) 
            inner join transaksi on id_pendapatan = jenis_transaksi 
            where transaksi.id=? and waktu between ? and ? group by jenis_pendapatan,id_jenis_pendapatan, group_category,group_category_id) as b 
            on b.id_jenis_pendapatan = a.id_jenis_pendapatan where a.id=?;";
        $id=Auth::user()->id;
        // dd(DB::select($q,[$id,$id,$start_default,$end_default]));
        return DB::select($q,[$id,$id,$start_default,$end_default,$id]);
    }

    public function selectByID($id)
    {
        $qSelectByID="
            SELECT id_jenis_pendapatan ,jenis_pendapatan,group_category_id, jenis_pendapatan.updated_at, jenis_pendapatan.created_at ,color,id
            FROM 
                jenis_pendapatan 
                    left join pendapatan using(id_jenis_pendapatan) 
                    inner join users using(id) 
            where users.id=? and email = ? and id_jenis_pendapatan = ?;
         ";
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $data = DB::select($qSelectByID,[$id_user,$email,$id]);
        return $data;
    }


    public function selectByName($name)
    {
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $data = DB::select($this->qSelectByName,[$id_user,$email,$name]);
        return $data;
    }
    public function GCPendapatan(){
        return DB::select("select group_category_id, pendapatan,gabung,pengeluaran, if(gabung=1,concat(group_category,' -> Synchronize'),concat(group_category,' -> Not Synchronize')) as group_category from group_category where pendapatan=1");
    }

    public function insertData($data)
    {
        DB::select('INSERT INTO jenis_pendapatan (id_jenis_pendapatan, jenis_pendapatan, group_category_id, updated_at, created_at, id, color) VALUES (?, ?, ?, ?,?, ? ,?)', $data);
    }

    public function updateByID($data)
    {
        DB::select('UPDATE jenis_pendapatan set jenis_pendapatan = ?, group_category_id = ? where id_jenis_pendapatan = ? and id = ? ', $data);
    }
    
    public function deleteByID($data){
        DB::select('DELETE from jenis_pendapatan where id_jenis_pendapatan = ? and id = ? ', $data);
    }
    public function deleteByName($data)
    {
        DB::select('DELETE from jenis_pendapatan where jenis_pendapatan = ? and group_category_id = ? and id = ? ', $data);
    }



    public function selectByGroup($group_category_id,$start_default, $end_default)
    {
        $data = DB::select("set @time = MONTHNAME(?), @year = DATE_FORMAT(?, '%Y')",[$start_default,$start_default]);
        $q="
            SELECT  id_jenis_pendapatan as id_kategori, jenis_pendapatan as kategori, ifnull(count, 0) as count, color, ifnull(total,0) as total, concat(@time,' ',@year) as bulan
            FROM jenis_pendapatan 
            LEFT JOIN 
            (
                SELECT id_kategori, kategori, count(kategori) as count, waktu, sum(jumlah) as total
                FROM 
                (
                    SELECT id_kategori, kategori, nama_pendapatan,waktu, jumlah 
                    FROM 
                    (
                        SELECT group_category_id, id_jenis_pendapatan as id_kategori, jenis_pendapatan as kategori, updated_at, color,created_at 
                        FROM jenis_pendapatan 
                        jumlah WHERE group_category_id = ? and id=?
                    ) kategori 
                    INNER join pendapatan on id_kategori = id_jenis_pendapatan 
                    INNER join transaksi on id_pendapatan = jenis_transaksi
                ) a 
                    WHERE waktu between ? and ?
                    GROUP by kategori
            ) b on id_jenis_pendapatan = id_kategori 
            WHERE id=? and group_category_id=?;";
        $id=Auth::user()->id;
        $data = DB::select($q,[$group_category_id,$id,$start_default, $end_default,$id,$group_category_id]);
        return $data;
    }


}
