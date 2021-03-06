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
    // public function selectAll(){
    //     $q="SELECT id_jenis_pengeluaran as id_kategori, jenis_pengeluaran, count(jumlah) as jt, sum(jumlah) as total, group_category, group_category_id from jenis_pengeluaran inner join group_category using(group_category_id) inner join pengeluaran using (id_jenis_pengeluaran) inner join transaksi on id_pengeluaran = jenis_transaksi where transaksi.id=? group by jenis_pengeluaran;";
    //     $id=Auth::user()->id;
    //     dd(DB::select($q,[$id]));
    //     return DB::select($q,[$id]);
    // }

    public function selectAll(){
        $q="SELECT id_jenis_pengeluaran as id_kategori, jenis_pengeluaran, group_category_id from jenis_pengeluaran where id = ? ";
        $id=Auth::user()->id;
        return DB::select($q,[$id]);
    }
    public function selectRange($start_default,$end_default){
        $q="SELECT a.id_jenis_pengeluaran, a.jenis_pengeluaran, ifnull(jt,0) as jt, ifnull(total,0) as total, a.group_category, a.group_category_id 
        from (
                select id_jenis_pengeluaran,jenis_pengeluaran, group_category, group_category_id, id from jenis_pengeluaran inner join group_category using(group_category_id) where id=?
            ) as a left join (
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
        $q="
            SELECT * FROM jenis_pengeluaran 
                    inner join group_category using(group_category_id)
            where id=?  and id_jenis_pengeluaran = ?;
         ";
         
        $data = DB::select($q,[Auth::user()->id,$id]);
        return $data;
    }
    public function selectByName($name)
    {
        return DB::table('pengeluaran')
        ->join('jenis_pengeluaran','pengeluaran.id_jenis_pengeluaran','=','jenis_pengeluaran.id_jenis_pengeluaran')
        ->join('group_category','jenis_pengeluaran.group_category_id','=','group_category.group_category_id')
        ->where([
            ['jenis_pengeluaran','=',$name],
            ['id','=',Auth::user()->id]
        ])->get();
    }
    public function GCPengeluaran(){
        return DB::select("select group_category_id, pendapatan,gabung,pengeluaran, note,
        if(gabung=1,concat(group_category,' -> [Pengeluaran <=> Pendapatan]'),concat(group_category,' -> [Pengeluaran]')) as group_category 
        from group_category where pengeluaran=1");
    }

    // public function sumByDate($time)
    // {
    //     // dd("");
    //     $id_user=Auth::user()->id;
    //     $data = DB::select($this->qByDate,[$id_user,$time]);
    //     // dd($data);
    //     return $data;
    // }

    // public function sumGroupByKategori($id_user,$start_default, $end_default)
    // {
    //     $data=DB::table('jenis_pengeluaran as jp')
    //                         ->select('jp.jenis_pengeluaran',DB::raw('ifnull(p.sum_kategori,0) as sum_kategori'))
    //                         ->leftJoin(DB::raw('(SELECT id_jenis_pengeluaran,sum(jumlah) as sum_kategori 
    //                                     FROM pengeluaran 
    //                                     inner join transaksi t on jenis_transaksi=id_pengeluaran
    //                                     WHERE id=(?) and waktu between ? and ? 
    //                                     GROUP BY id_jenis_pengeluaran) as p
    //                                 '),
    //                             function($join)
    //                             {
    //                                 $join->on('jp.id_jenis_pengeluaran', '=', 'p.id_jenis_pengeluaran');
    //                             })
    //                         ->where('jp.id','=','?')
    //                         ->orderBy('jp.jenis_pengeluaran', 'asc')
    //                         ->setBindings([ $id_user,$start_default, $end_default,$id_user])
    //                         ->get();
    //     return $data;
    // }

    public function insertData($data)
    {
        DB::select('INSERT INTO jenis_pengeluaran (id_jenis_pengeluaran, jenis_pengeluaran, group_category_id, updated_at, created_at, id, color) VALUES (?, ?, ?, ?,?, ? ,?)', $data);
    }

    public function updateByID($data)
    {
        DB::select('UPDATE jenis_pengeluaran set jenis_pengeluaran = ?, group_category_id = ? where id_jenis_pengeluaran = ? and id = ? ', $data);
    }
    
    public function deleteByID($data){
        DB::select('DELETE from jenis_pengeluaran where id_jenis_pengeluaran = ? and id = ? ', $data);
    }
    public function deleteByName($data)
    {
        DB::select('DELETE from jenis_pengeluaran where jenis_pengeluaran = ? and group_category_id = ? and id = ? ', $data);
    }



    // public function selectByGroup($group_category_id,$start_default, $end_default)
    // {
    //     $data = DB::select("set @time = MONTHNAME(?), @year = DATE_FORMAT(?, '%Y')",[$start_default,$start_default]);
    //     $q="
    //         SELECT  id_jenis_pengeluaran as id_kategori, jenis_pengeluaran as kategori, ifnull(count, 0) as count, color, ifnull(total,0) as total, concat(@time,' ',@year) as bulan
    //         FROM jenis_pengeluaran 
    //         LEFT JOIN 
    //         (
    //             SELECT id_kategori, kategori, count(kategori) as count, waktu, sum(jumlah) as total
    //             FROM 
    //             (
    //                 SELECT id_kategori, kategori, nama_pengeluaran,waktu, jumlah 
    //                 FROM 
    //                 (
    //                     SELECT group_category_id, id_jenis_pengeluaran as id_kategori, jenis_pengeluaran as kategori, updated_at, color,created_at 
    //                     FROM jenis_pengeluaran 
    //                     jumlah WHERE group_category_id = ? and id=?
    //                 ) kategori 
    //                 INNER join pengeluaran on id_kategori = id_jenis_pengeluaran 
    //                 INNER join transaksi on id_pengeluaran = jenis_transaksi
    //             ) a 
    //                 WHERE waktu between ? and ?
    //                 GROUP by kategori
    //         ) b on id_jenis_pengeluaran = id_kategori 
    //         WHERE id=? and group_category_id=?;";
    //     $id=Auth::user()->id;
    //     $data = DB::select($q,[$group_category_id,$id,$start_default, $end_default,$id,$group_category_id]);
    //     return $data;
    // }

    public function selectAllByGC($id_user,$start_default, $end_default){
        $q="select a.group_category_id, a.group_category, ifnull(id_jenis_pengeluaran,'0') as id_jenis_pengeluaran, 
        ifnull(jenis_pengeluaran,'0') as jenis_pengeluaran,ifnull(color,'rgb(78,115,223)') as color, 
        count(nama_pengeluaran) as jt,
        sum(ifnull(jumlah,0)) as total, 
        round((sum(ifnull(jumlah,0))/(b.danakeluar))*100,2) persen
        from group_category as a 
        left join (
                    select group_category_id, id_pengeluaran, id_jenis_pengeluaran,nama_pengeluaran, jumlah, waktu, jenis_pengeluaran, color, group_category,
                    (
                        SELECT ifnull(sum(jumlah),0) as total 
                        FROM transaksi 
                        inner join pengeluaran on jenis_transaksi=id_pengeluaran 
                        where id=? and waktu between ? and ?
                    ) as danakeluar
                    from transaksi 
                    inner join pengeluaran on jenis_transaksi = id_pengeluaran 
                    inner join jenis_pengeluaran using(id_jenis_pengeluaran) 
                    inner join group_category using(group_category_id)
                    where transaksi.id = ? and waktu between ? and ?
                ) as b using(group_category_id) 
        where a.pengeluaran = 1
        group by a.group_category, a.group_category_id 
        order by total desc, a.group_category desc";
        return DB::select($q,[$id_user,$start_default,$end_default,$id_user,$start_default,$end_default]);
    }
    public function selectAllByGCID($id,$start_default,$end_default,$group_category_id){
        $q="SELECT a.color,a.id_jenis_pengeluaran, a.jenis_pengeluaran, ifnull(jt,0) as jt, ifnull(total,0) as total, ifnull(round((total/danakeluar)*100,2),0) as persen, a.group_category, a.group_category_id
        from (
                select id_jenis_pengeluaran,jenis_pengeluaran, group_category, group_category_id, id, color,
                (
                    SELECT ifnull(sum(jumlah),0) as total 
                    FROM transaksi 
                    inner join pengeluaran on jenis_transaksi=id_pengeluaran 
                    where id=? and waktu between ? and ?
                ) as danakeluar
                from jenis_pengeluaran inner join group_category using(group_category_id) where id=?) as a left join (
                SELECT id_jenis_pengeluaran, jenis_pengeluaran, count(jumlah) as jt, sum(jumlah) as total, group_category, group_category_id 
                from jenis_pengeluaran 
                inner join group_category using(group_category_id) 
                inner join pengeluaran using (id_jenis_pengeluaran) 
                inner join transaksi on id_pengeluaran = jenis_transaksi 
                where transaksi.id=? and waktu between ? and ? group by jenis_pengeluaran,id_jenis_pengeluaran, group_category,group_category_id
            ) as b on b.id_jenis_pengeluaran = a.id_jenis_pengeluaran 
            where a.id=? and a.group_category_id=?;";
        return DB::select($q,[$id,$start_default,$end_default,$id,$id,$start_default,$end_default,$id,$group_category_id]);
    }


}
