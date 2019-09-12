<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JenisPendapatan extends Model
{

	protected $table ="jenis_pendapatan";
	public $timestamps = false;
	protected $fillable = ['id_jenis_pendapatan','jenis_pendapatan'];

    private $qSelectAll="SELECT a.id_jenis_pendapatan, a.jenis_pendapatan, sum(b.jumlah) as total, if(b.jumlah>0,count(a.id_jenis_pendapatan),'0') as jumlah_data, a.created_at as created_at from jenis_pendapatan as a left join (select id_jenis_pendapatan, jumlah from pendapatan inner join transaksi on id_pendapatan = jenis_transaksi) as b on a.id_jenis_pendapatan = b.id_jenis_pendapatan where a.id=? group by a.id_jenis_pendapatan ORDER BY a.created_at asc;";

    private $qSelectByName="
        SELECT id_jenis_pendapatan,jenis_pendapatan,group_category_id,color,id 
        FROM 
            jenis_pendapatan 
                left join pendapatan using(id_jenis_pendapatan) 
                inner join users using(id) 
        where users.id=? and email = ? and jenis_pendapatan = ?;
     ";

    private $qPendByKat = "
        SELECT * 
        FROM pendapatan 
        LEFT JOIN transaksi 
        ON jenis_transaksi = id_pendapatan 
        WHERE id=?
        AND id_jenis_pendapatan = ? ";

    public function selectAll(){
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $data = DB::select($this->qSelectAll,[$id_user]);
        return $data;
    }

    public function pendByKat($id)
    {
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $data = DB::select($this->qPendByKat,[$id_user,$id]);
        return $data;
    }

    // Pendapatan By ID
    public function selectByID($id)
    { 
        $qSelectByID="
            SELECT id_jenis_pendapatan as id_kategori ,jenis_pendapatan as kategori,group_category_id,jenis_pendapatan.updated_at, jenis_pendapatan.created_at,color,id
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

    // public function pendByKat($id)
    // {
    //     $id_user=Auth::user()->id;
    //     $email=Auth::user()->email;
    //     $data = DB::select($this->qPendByKat,[$id_user,$id]);
    //     return $data;
    // }

    public function selectByName($name)
    {
        $id_user=Auth::user()->id;
        $email=Auth::user()->email;
        $data = DB::select($this->qSelectByName,[$id_user,$email,$name]);
        return $data;
    }

    public function insertData($data)
    {
        DB::select('INSERT INTO jenis_pendapatan (id_jenis_pendapatan, jenis_pendapatan, group_category_id, updated_at, created_at,id,color) VALUES (?, ?, ?, ?,?,?,?)', $data);
    }
    public function updateByID($data)
    {
        DB::select('UPDATE jenis_pendapatan set jenis_pendapatan = ?, group_category_id = ? where id_jenis_pendapatan = ? and id = ? ', $data);
    }
    public function deleteByName($data)
    {
        DB::select('DELETE from jenis_pendapatan where jenis_pendapatan = ? and group_category_id = ? and id = ? ', $data);
    }


    public function deleteByID($data)
    {
        DB::select('DELETE from jenis_pendapatan where id_jenis_pendapatan = ? and id = ? ', $data);
    }



    public function selectByGroup($group_category_id,$start_default, $end_default)
    {
        $q="
            SELECT  id_jenis_pendapatan as id_kategori, jenis_pendapatan as kategori, ifnull(count, 0) as count, color, ifnull(total,0) as total
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

        // $q="
        //     SELECT  id_jenis_pendapatan as id_kategori, jenis_pendapatan as kategori, ifnull(count, 0) as total, color, ifnull(total,0) as total
        //     FROM jenis_pendapatan 
        //     LEFT JOIN 
        //     (
        //         SELECT id_kategori, kategori, count(kategori) as count, waktu, sum(jumlah) as total
        //         FROM 
        //         (
        //             SELECT id_kategori, kategori, nama_pendapatan,waktu, jumlah 
        //             FROM 
        //             (
        //                 SELECT group_category_id, id_jenis_pendapatan as id_kategori, jenis_pendapatan as kategori, updated_at, color,created_at 
        //                 FROM jenis_pendapatan 
        //                 jumlah WHERE group_category_id = 1 and id=1
        //             ) kategori 
        //             INNER join pendapatan on id_kategori = id_jenis_pendapatan 
        //             INNER join transaksi on id_pendapatan = jenis_transaksi
        //         ) a 
        //             WHERE waktu between '2019-01-01' and '2019-01-31'
        //             GROUP by kategori
        //     ) b on id_jenis_pendapatan = id_kategori 
        //     WHERE id=1 and group_category_id=1;";
        $id=Auth::user()->id;
        $data = DB::select($q,[$group_category_id,$id,$start_default, $end_default,$id,$group_category_id]);
        return $data;
    }
}
