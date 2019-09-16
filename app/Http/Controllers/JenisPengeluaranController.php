<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisPengeluaran;
use App\JenisPendapatan;
use App\Pendapatan;
use App\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Auth;

class JenisPengeluaranController extends Controller
{

    public function selectAll(){
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $data = array(
            'group_category_peng'=> DB::select("select * from group_category where pengeluaran = 1"),
            'all_jenis_pengeluaran' => $katPengeluaran->selectAll(),
            'all_jenis_pendapatan' => $katPendapatan->selectAll()
        );
        return $data;
    }

    public function store(Request $request){
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $id_user = Auth::user()->id;
        $rules = array(
            'jenis_pengeluaran' => [ 'required', 'regex:/^[ a-zA-Z&-]+$/u',
                Rule::unique('jenis_pengeluaran')->where(function ($query) {
                    $query->where('id', Auth::user()->id);
                }),
            ],
            'group_category_id' => ['required','exists:group_category']
        );
        $customMessages = [
            'jenis_pengeluaran.required' => '<b>Nama Kategori</b> - Belum di isi',
            'jenis_pengeluaran.regex' => '<b>Nama Kategori</b> - karakter tidak diperbolehkan',
            'group_category_id.required' => '<b>Group Category<b> invalid',
            'group_category_id.exists' => '<b>Group Category</b> Belum di pilih'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        // 1. check group category
        $dataGC = DB::table("group_category")->where('group_category_id',$validator['group_category_id'])->get();
        if (count($dataGC)==0) {
            return response()->json(['errors' => ['group kategori gak ada!!']], 422);
        }

        // 2. Jenis Pendapatan tidak sama dengan Jenis Pengeluaran
        $checkTransaksiDM = $katPendapatan->selectByName($validator['jenis_pengeluaran']);
        if(count($checkTransaksiDM)>=1){
            return response()->json(['errors' => ['Kategori <b>'.$validator['jenis_pengeluaran'].'</b> sudah ada. Group <b>'.$checkTransaksiDM[0]->group_category.'</b>']], 422);
        }


        $data = array(
            'KTG_'.uniqid(),
            $validator['jenis_pengeluaran'],
            $validator['group_category_id'],
            date("Y-m-d H:i:s"),
            date("Y-m-d H:i:s"),
            $id_user,
            $this->randomRGB()
        );

        if ($dataGC[0]->gabung==0) {
            $katPengeluaran->insertData($data);
        }
        
        if ($dataGC[0]->gabung==1) {
            $katPengeluaran->insertData($data);
            $katPendapatan->insertData($data);
        }

        return [
            'url'=>'/kategori/danakeluar',
        ];
    }

    public function edit(Request $request){
        $katPengeluaran = new JenisPengeluaran;
        $rules = array(
            'id_jenis_pengeluaran' => 'required|exists:jenis_pengeluaran'
        );
        $customMessages = [
            'id_jenis_pengeluaran.required' => 'ID Jenis Pengeluaran invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $dataJenisPengeluaran = DB::table('jenis_pengeluaran')->where([
            ['id_jenis_pengeluaran','=',$validator['id_jenis_pengeluaran']],
            ['id','=',Auth::user()->id],
        ])->get();
        // dd($dataJenisPengeluaran);
        $data = array(
            'editData' => $dataJenisPengeluaran
        );

        return $data;
    }

    public function update(Request $request)
    {
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $pengeluaran = new Pengeluaran;
        $id_user=Auth::user()->id;
        $rules = array(
            'id_jenis_pengeluaran' => 'required|exists:jenis_pengeluaran',
            'jenis_pengeluaran' => 'required',
            'group_category_id' => 'required|exists:group_category'
        );
        // dd($request);
        $customMessages = [
            'id_jenis_pengeluaran.required' => 'ID Jenis Pendapatan invalid',
            'group_category_id.required' => 'Group Category invalid'
        ];
        
        $validator = $this->validate($request, $rules, $customMessages);
        $checkJenisPengeluaranBefore = $katPengeluaran->selectByID($validator['id_jenis_pengeluaran']);
        
        $checkGroupCategory = DB::table("group_category")->where([['group_category.group_category_id',$validator['group_category_id']]])->get();
        
        $dataKategori = array($validator['jenis_pengeluaran'],$validator['group_category_id'],$validator['id_jenis_pengeluaran'],$id_user);
        
        if ($checkJenisPengeluaranBefore[0]->gabung==0 && $checkGroupCategory[0]->gabung==0) {
            $katPengeluaran->updateByID($dataKategori);
        }elseif ($checkJenisPengeluaranBefore[0]->gabung==1 && $checkGroupCategory[0]->gabung==1) {
            $katPendapatan->updateByID($dataKategori);
            $katPengeluaran->updateByID($dataKategori);
        }else{
            return response()->json(['errors' => ["Kategori Sync. atau Not Sync. tidak dapat ditukar"]], 422);
        }

        return [
            'url'=>'/kategori/danakeluar',
        ];

    }

    public function delete(Request $request)
    {
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $pendapatan = new Pendapatan;
        $pengeluaran = new Pengeluaran;
        $id_user=Auth::user()->id; 
        
        $rules = array(
            'id_jenis_pengeluaran' => [ 'required', 'regex:/^[0-9_a-zA-Z&-]+$/u','max:255',
                Rule::exists('jenis_pengeluaran')->where(function ($query) {
                    $id=Auth::user()->id;
                    $query->where('id', $id);
                }),
            ],

        );
        $customMessages = [
            'id_jenis_pengeluaran.required' => 'ID Jenis Pendapatan invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);

        $dataPendapatan = [$validator['id_jenis_pengeluaran'],$id_user];

        $checkGroupCategory = DB::table("jenis_pengeluaran")
        ->join('group_category','jenis_pengeluaran.group_category_id','=','group_category.group_category_id')
        ->where([
            ['jenis_pengeluaran.id_jenis_pengeluaran',$validator['id_jenis_pengeluaran']],
            ['id',$id_user],
            ])->get();
        
        if ($checkGroupCategory[0]->gabung==0) {
            $checkTransaksiDK = $pengeluaran->selectByIDJPG($validator['id_jenis_pengeluaran']);
            
            if (count($checkTransaksiDK)>=1) {
                $urlDM="<a href='/danakeluar/kategori/".$validator['id_jenis_pengeluaran']."/'>Lihat ".count($checkTransaksiDK)." Data Pengeluaran </a>";
                return response()->json(['errors' => ['Kategori <b>'.$checkTransaksiDK[0]->jenis_pengeluaran."</b> Masih digunakan. $urlDM"]], 422);
            }
            $katPengeluaran->deleteByID($dataPendapatan);
        }

        if ($checkGroupCategory[0]->gabung==1) {
            $checkTransaksiDM = $pendapatan->selectByIDJPD($validator['id_jenis_pengeluaran']);
            $checkTransaksiDK = $pengeluaran->selectByIDJPG($validator['id_jenis_pengeluaran']);
            
            if (count($checkTransaksiDM)>=1 || count($checkTransaksiDK)>=1) {
                $urlDM="<a href='/danamasuk/kategori/".$validator['id_jenis_pengeluaran']."/'>Check ".count($checkTransaksiDM)." Data Pendapatan</a>";
                $urlDK="<a href='/danakeluar/kategori/".$validator['id_jenis_pengeluaran']."/'>Check ".count($checkTransaksiDK)." Data Pengeluaran</a>";
                return response()->json(['errors' => ['Kategori <b>'.$checkGroupCategory[0]->jenis_pengeluaran."</b> Masih digunakan. $urlDM & $urlDK "]], 422);
            }
            $katPendapatan->deleteByID($dataPendapatan);
            $katPengeluaran->deleteByID($dataPendapatan);
        }
        return [
            'pesan' => '1',
            'url'=>'/kategori/danakeluar',
        ];
    }


    // public function viewPengeluaranByJPG(){
    //     return view('app_keuangan.jenis_pengeluaran.danaKeluarByKategori');
    // }
    public function viewPengByKat($id){
        $katPengeluaran = new JenisPengeluaran;
        $kategori = $katPengeluaran->selectByID($id);
        $dataKatByID = $katPengeluaran->pengByKat($id);
        // dd($katPengeluaran->selectByID($id));
        // $data = $this->dataDefault($time);
        $time = date("Y-m-d");
        $data=$this->dataDefault($time);
        $data['kategori'] = $kategori[0]->jenis_pengeluaran;
        $data['dataKatByID'] = $katPengeluaran->pengByKat($id);
        // return $data;
        return view('app_keuangan.jenis_Pengeluaran.kategori_by_id')->with($data);
        // dd($data);
    }

    public function getDataPengeluaranByJPG(Request $request){
        $katPengeluaran = new JenisPengeluaran;
        $rules = array(
            'id_jenis_pengeluaran' => 'required|exists:jenis_pengeluaran'
        );
        $customMessages = [
            'id_jenis_pengeluaran.required' => 'ID Jenis Pengeluaran invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);

        // check jumlah data yang digunakan terkait jenis pengeluaran ini
        $dataPengeluaranByKat = DB::select('SELECT * from pengeluaran inner join transaksi on id_pengeluaran = jenis_transaksi where id_jenis_pengeluaran = ? ',[$validator['id_jenis_pengeluaran']]); 
        
        $dataKatByID = DB::select('SELECT * from jenis_pengeluaran where id_jenis_pengeluaran = ? limit 1',[$validator['id_jenis_pengeluaran']]); 

        $jumlah = count($dataPengeluaranByKat);
        if ($jumlah>0) {
            $data = array(
                'dataKatByID' => $dataKatByID,
                'dataPengeluaranByKat' => $dataPengeluaranByKat,
            );
            return $data;

        }else{
            // $url = "#/jenis_pengeluaran/{$validator['id_jenis_pengeluaran']}";
            // $pesan = "terdapat {$jumlah} data menggunakan kategori ini <a href={$url} class='alert-link'>Lihat data</a>";
            // $error = [ 'id_jenis_pengeluaran' => $pesan];
            // return response()->json(['message' => 'data yang dikirimkan salah', 'errors' => $error], 422)
            
        }
    }









}
?>