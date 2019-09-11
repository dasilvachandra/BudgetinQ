<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JenisPengeluaran extends Model
{
	protected $table ="jenis_pengeluaran";
	public $timestamps = false;
	// protected $fillable = ['nama_pengeluaran','jumlah'];

    private $qSelectByName="
        SELECT id_jenis_pengeluaran,jenis_pengeluaran,group_category_id,color,id
        FROM 
            jenis_pengeluaran 
                left join pengeluaran using(id_jenis_pengeluaran) 
                inner join users using(id) 
        where users.id=? and email = ? and jenis_pengeluaran = ?;
     ";
    private $qPengByKat = "
        SELECT * 
        FROM pengeluaran 
        LEFT JOIN transaksi 
        ON jenis_transaksi = id_pengeluaran 
        WHERE id=?
        AND id_jenis_pengeluaran = ? ";

    private $qByDate = '
        SELECT ifnull((
            select sum(jumlah)
            from pengeluaran inner join transaksi on jenis_transaksi = id_pengeluaran 
            where id=? and waktu=?),"0") as jumlah
    ';
    public function selectAll(){
        $q="SELECT id_jenis_pengeluaran, jenis_pengeluaran, count(jumlah) as jt, sum(jumlah) as total, group_category, group_category_id from jenis_pengeluaran inner join group_category using(group_category_id) inner join pengeluaran using (id_jenis_pengeluaran) inner join transaksi on id_pengeluaran = jenis_transaksi where transaksi.id=? group by jenis_pengeluaran;";
        $id=Auth::user()->id;
        return DB::select($q,[$id]);
    }
    
    public function selectRange($start_default,$end_default){
        $q="SELECT a.id_jenis_pengeluaran, a.jenis_pengeluaran, ifnull(jt,0) as jt, ifnull(total,0) as total, ifnull(group_category,0) as group_category, a.group_category_id 
        from (select * from jenis_pengeluaran where id=?) as a left join (
            SELECT id_jenis_pengeluaran, jenis_pengeluaran, count(jumlah) as jt, sum(jumlah) as total, group_category, group_category_id 
            from jenis_pengeluaran 
            inner join group_category using(group_category_id) 
            inner join pengeluaran using (id_jenis_pengeluaran) 
            inner join transaksi on id_pengeluaran = jenis_transaksi 
            where transaksi.id=? and waktu between ? and ? group by jenis_pengeluaran,id_jenis_pengeluaran, group_category,group_category_id) as b 
            on b.id_jenis_pengeluaran = a.id_jenis_pengeluaran where a.id=?;";
        $id=Auth::user()->id;
        // dd(DB::select($q,[$id,$id,$start_default,$end_default]));
        return DB::select($q,[$id,$id,$start_default,$end_default,$id]);
    }

    public function selectByID($id)
    {
        $qSelectByID="
            SELECT id_jenis_pengeluaran as id_kategori ,jenis_pengeluaran as kategori,group_category_id, jenis_pengeluaran.updated_at, jenis_pengeluaran.created_at ,color,id
            FROM 
                jenis_pengeluaran 
                    left join pengeluaran using(id_jenis_pengeluaran) 
                    inner join users using(id) 
            where users.id=? and email = ? and id_jenis_pengeluaran = ?;
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

    public function sumByDate($time)
    {
        // dd("");
        $id_user=Auth::user()->id;
        $data = DB::select($this->qByDate,[$id_user,$time]);
        // dd($data);
        return $data;
    }

    public function sumGroupByKategori($id_user,$start_default, $end_default)
    {
        $data=DB::table('jenis_pengeluaran as jp')
                            ->select('jp.jenis_pengeluaran',DB::raw('ifnull(p.sum_kategori,0) as sum_kategori'))
                            ->leftJoin(DB::raw('(SELECT id_jenis_pengeluaran,sum(jumlah) as sum_kategori 
                                        FROM pengeluaran 
                                        inner join transaksi t on jenis_transaksi=id_pengeluaran
                                        WHERE id=(?) and waktu between ? and ? 
                                        GROUP BY id_jenis_pengeluaran) as p
                                    '),
                                function($join)
                                {
                                    $join->on('jp.id_jenis_pengeluaran', '=', 'p.id_jenis_pengeluaran');
                                })
                            ->where('jp.id','=','?')
                            ->orderBy('jp.jenis_pengeluaran', 'asc')
                            ->setBindings([ $id_user,$start_default, $end_default,$id_user])
                            ->get();
        return $data;
    }

    public function insertData($data)
    {
        // array_push($data,$color_randong);
        DB::select('INSERT INTO jenis_pengeluaran (id_jenis_pengeluaran, jenis_pengeluaran, group_category_id, updated_at, created_at, id, color) VALUES (?, ?, ?, ?,?, ? ,?)', $data);
    }
    public function updateByID($data)
    {
        DB::select('UPDATE jenis_pengeluaran set jenis_pengeluaran = ?, group_category_id = ? where id_jenis_pengeluaran = ? and id = ? ', $data);
    }
    
    public function updateByName($data)
    {
        DB::select('UPDATE jenis_pengeluaran set jenis_pengeluaran = ?, group_category_id = ? where jenis_pengeluaran = ? and id = ? and group_category_id = ? ', $data);
    }

    public function updateByName2($data)
    {
        DB::select('UPDATE jenis_pengeluaran set jenis_pengeluaran = ? where jenis_pengeluaran = ? and id = ?', $data);
    }

    public function deleteByID($data){
        DB::select('DELETE from jenis_pengeluaran where id_jenis_pengeluaran = ? and id = ? ', $data);
    }
    public function deleteByName($data)
    {
        DB::select('DELETE from jenis_pengeluaran where jenis_pengeluaran = ? and group_category_id = ? and id = ? ', $data);
    }



    public function selectByGroup($group_category_id,$start_default, $end_default)
    {
        $data = DB::select("set @time = MONTHNAME(?), @year = DATE_FORMAT(?, '%Y')",[$start_default,$start_default]);
        $q="
            SELECT  id_jenis_pengeluaran as id_kategori, jenis_pengeluaran as kategori, ifnull(count, 0) as count, color, ifnull(total,0) as total, concat(@time,' ',@year) as bulan
            FROM jenis_pengeluaran 
            LEFT JOIN 
            (
                SELECT id_kategori, kategori, count(kategori) as count, waktu, sum(jumlah) as total
                FROM 
                (
                    SELECT id_kategori, kategori, nama_pengeluaran,waktu, jumlah 
                    FROM 
                    (
                        SELECT group_category_id, id_jenis_pengeluaran as id_kategori, jenis_pengeluaran as kategori, updated_at, color,created_at 
                        FROM jenis_pengeluaran 
                        jumlah WHERE group_category_id = ? and id=?
                    ) kategori 
                    INNER join pengeluaran on id_kategori = id_jenis_pengeluaran 
                    INNER join transaksi on id_pengeluaran = jenis_transaksi
                ) a 
                    WHERE waktu between ? and ?
                    GROUP by kategori
            ) b on id_jenis_pengeluaran = id_kategori 
            WHERE id=? and group_category_id=?;";
        $id=Auth::user()->id;
        $data = DB::select($q,[$group_category_id,$id,$start_default, $end_default,$id,$group_category_id]);
        return $data;
    }


}
