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

class JenisPendapatanController extends Controller
{

    public function selectAll(){
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $data = array(
            'group_category_pend'=> DB::select("select * from group_category where pendapatan = 1"),
            'all_jenis_pendapatan' => $katPengeluaran->selectAll(),
            'all_jenis_pendapatan' => $katPendapatan->selectAll()
        );
        return $data;
    }

    public function store(Request $request){
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $id_user = Auth::user()->id;
        $rules = array(
            'jenis_pendapatan' => [ 'required', 'regex:/^[ a-zA-Z&-]+$/u',
                Rule::unique('jenis_pendapatan')->where(function ($query) {
                    $query->where('id', Auth::user()->id);
                }),
            ],
            'group_category_id' => ['required','regex:/^[1-6]+$/u','integer','max:6']
        );
        $customMessages = [
            'jenis_pendapatan.required' => 'Nama Kategori - Belum di isi',
            'jenis_pendapatan.regex' => 'Nama Kategori - karakter tidak diperbolehkan',
            'group_category_id.required' => 'Group Category invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);

        // 1. Jenis Pendapatan tidak sama dengan Jenis Pengeluaran
        $checkTransaksiDK = $katPengeluaran->selectByName($validator['jenis_pendapatan']);
        if(count($checkTransaksiDK)>=1){
            return response()->json(['errors' => ['Kategori <b>'.$validator['jenis_pendapatan'].'</b> sudah ada. Group <b>'.$checkTransaksiDK[0]->group_category.'</b>']], 422);
        }

        $dataGC = DB::table("group_category")->where('group_category_id',$validator['group_category_id'])->get();
        if (count($dataGC)==0) {
            return response()->json(['errors' => ['group kategori gak ada!!']], 422);
        }

        $data = array(
            'KTG_'.uniqid(),
            $validator['jenis_pendapatan'],
            $validator['group_category_id'],
            date("Y-m-d H:i:s"),
            date("Y-m-d H:i:s"),
            $id_user,
            $this->randomRGB()
        );

        if ($dataGC[0]->gabung==0) {
            $katPendapatan->insertData($data);
        }
        
        if ($dataGC[0]->gabung==1) {
            $katPengeluaran->insertData($data);
            $katPendapatan->insertData($data);
        }

        return [
            'url'=>'/kategori/danamasuk',
        ];
    }

    public function edit(Request $request){
        $katPengeluaran = new JenisPengeluaran;
        $rules = array(
            'id_jenis_pendapatan' => 'required|exists:jenis_pendapatan'
        );
        $customMessages = [
            'id_jenis_pendapatan.required' => 'ID Jenis Pendapatan invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $dataJenisPendapatan = DB::table('jenis_pendapatan')->where([
            ['id_jenis_pendapatan','=',$validator['id_jenis_pendapatan']],
            ['id','=',Auth::user()->id],
        ])->get();
        // dd($dataJenisPendapatan);
        $data = array(
            'editData' => $dataJenisPendapatan
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
            'id_jenis_pendapatan' => 'required|exists:jenis_pendapatan',
            'jenis_pendapatan' => 'required',
            'group_category_id' => 'required|exists:group_category'
        );
        // dd($request);
        $customMessages = [
            'id_jenis_pendapatan.required' => 'ID Jenis Pendapatan invalid',
            'group_category_id.required' => 'Group Category invalid'
        ];
        
        $validator = $this->validate($request, $rules, $customMessages);
        $checkJenisPendapatanBefore = DB::table("jenis_pendapatan")
                                    ->join('group_category','jenis_pendapatan.group_category_id','=','group_category.group_category_id')
                                    ->where([
                                        ['jenis_pendapatan.id_jenis_pendapatan',$validator['id_jenis_pendapatan']],
                                        ['id',$id_user],
                                    ])
                                    ->get();
        $checkGroupCategory = DB::table("group_category")
                                ->where([
                                    ['group_category.group_category_id',$validator['group_category_id']]
                                ])->get();
 
        $dataKategori = array($validator['jenis_pendapatan'],$validator['group_category_id'],$validator['id_jenis_pendapatan'],$id_user);
        
        if ($checkGroupCategory[0]->gabung==0) {
            $katPendapatan->updateByID($dataKategori);
            if($checkJenisPendapatanBefore[0]->gabung==1){
                $checkTransaksiDM = $pengeluaran->selectByIDJPD($validator['id_jenis_pendapatan']);
                if (count($checkTransaksiDM)==0) {
                    $katPengeluaran->deleteByID([$validator['id_jenis_pendapatan'],$id_user]);
                }else{
                    return response()->json(['errors' => [$checkTransaksiDM[0]->jenis_pendapatan." Masih terikat dengan ".count($checkTransaksiDM)." data danamasuk"]], 422);                }
                
            }
        }

        if ($checkGroupCategory[0]->gabung==1) {
            $katPendapatan->updateByID($dataKategori);
            if($checkJenisPendapatanBefore[0]->gabung==0){
                $dataInsert = array(
                    $validator['id_jenis_pendapatan'],
                    $validator['jenis_pendapatan'],
                    $validator['group_category_id'],
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                    $id_user,
                    $this->randomRGB()
                );
                $katPengeluaran->insertData($dataInsert);
            }
            $katPengeluaran->updateByID($dataKategori);
        }

        return [
            'url'=>'/kategori/danamasuk',
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
            'id_jenis_pendapatan' => [ 'required', 
                Rule::exists('jenis_pendapatan')->where(function ($query) {
                    $id=Auth::user()->id;
                    $query->where('id', $id);
                }),
            ],

        );
        $customMessages = [
            'id_jenis_pendapatan.required' => 'ID Jenis Pendapatan invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        // 1. Check apakah jenis_pendapatan sync. masih di digunakan di transaksi pengeluaran
        // $checkTransaksiDK = $katPengeluaran->selectByID($validator['jenis_pendapatan']);
        // if(count($checkTransaksiDK)>=1){
        //     return response()->json(['errors' => ['Kategori <b>'.$validator['jenis_pendapatan'].'</b> Masih digunakan. Group <b>'.$checkTransaksiDK[0]->group_category.'</b>']], 422);
        // }

        $dataPendapatan = [$validator['id_jenis_pendapatan'],$id_user];
        $checkGroupCategory = DB::table("jenis_pendapatan")
        ->join('group_category','jenis_pendapatan.group_category_id','=','group_category.group_category_id')
        ->where([
            ['jenis_pendapatan.id_jenis_pendapatan',$validator['id_jenis_pendapatan']],
            ['id',$id_user],
            ])->get();

        if ($checkGroupCategory[0]->gabung==0) {
            $checkTransaksiDM = $pendapatan->selectByIDJPD($validator['id_jenis_pendapatan']);
            if (count($checkTransaksiDM)>=1) {
                $urlDM="<a href='/danamasuk/kategori/".$validator['id_jenis_pendapatan']."/'>Lihat ".count($checkTransaksiDM)." Data Pendapatan </a>";
                return response()->json(['errors' => ['Kategori <b>'.$checkTransaksiDM[0]->jenis_pendapatan."</b> Masih digunakan. $urlDM"]], 422);
            }
            $katPendapatan->deleteByID($dataPendapatan);
        }

        if ($checkGroupCategory[0]->gabung==1) {
            $checkTransaksiDM = $pendapatan->selectByIDJPD($validator['id_jenis_pendapatan']);
            $checkTransaksiDK = $pengeluaran->selectByIDJPG($validator['id_jenis_pendapatan']);
            
            if (count($checkTransaksiDM)>=1 || count($checkTransaksiDK)>=1) {
                $urlDM="<a href='/danamasuk/kategori/".$validator['id_jenis_pendapatan']."/'>Check ".count($checkTransaksiDM)." Data Pendapatan</a>";
                $urlDK="<a href='/danakeluar/kategori/".$validator['id_jenis_pendapatan']."/'>Check ".count($checkTransaksiDK)." Data Pengeluaran</a>";
                return response()->json(['errors' => ['Kategori <b>'.$checkGroupCategory[0]->jenis_pendapatan."</b> Masih digunakan. $urlDM & $urlDK "]], 422);
            }
            dd($checkTransaksiDK);
            
            $katPendapatan->deleteByID($dataPendapatan);
            $katPengeluaran->deleteByID($dataPendapatan);
        }
        return [
            'url'=>'/kategori/danamasuk',
        ];
    }
    // public function viewPengeluaranByJPG(){
    //     return view('app_keuangan.jenis_pendapatan.danaKeluarByKategori');
    // }
    public function viewPengByKat($id){
        $katPengeluaran = new JenisPengeluaran;
        $kategori = $katPengeluaran->selectByID($id);
        $dataKatByID = $katPengeluaran->pengByKat($id);
        // dd($katPengeluaran->selectByID($id));
        // $data = $this->dataDefault($time);
        $time = date("Y-m-d");
        $data=$this->dataDefault($time);
        $data['kategori'] = $kategori[0]->jenis_pendapatan;
        $data['dataKatByID'] = $katPengeluaran->pengByKat($id);
        // return $data;
        return view('app_keuangan.jenis_Pengeluaran.kategori_by_id')->with($data);
        // dd($data);
    }

    public function getDataPengeluaranByJPG(Request $request){
        $katPengeluaran = new JenisPengeluaran;
        $rules = array(
            'id_jenis_pendapatan' => 'required|exists:jenis_pendapatan'
        );
        $customMessages = [
            'id_jenis_pendapatan.required' => 'ID Jenis Pendapatan invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);

        // check jumlah data yang digunakan terkait jenis pengeluaran ini
        $dataPendapatanByKat = DB::select('SELECT * from pengeluaran inner join transaksi on id_pengeluaran = jenis_transaksi where id_jenis_pendapatan = ? ',[$validator['id_jenis_pendapatan']]); 
        
        $dataKatByID = DB::select('SELECT * from jenis_pendapatan where id_jenis_pendapatan = ? limit 1',[$validator['id_jenis_pendapatan']]); 

        $jumlah = count($dataPendapatanByKat);
        if ($jumlah>0) {
            $data = array(
                'dataKatByID' => $dataKatByID,
                'dataPendapatanByKat' => $dataPendapatanByKat,
            );
            return $data;

        }else{
            // $url = "#/jenis_pendapatan/{$validator['id_jenis_pendapatan']}";
            // $pesan = "terdapat {$jumlah} data menggunakan kategori ini <a href={$url} class='alert-link'>Lihat data</a>";
            // $error = [ 'id_jenis_pendapatan' => $pesan];
            // return response()->json(['message' => 'data yang dikirimkan salah', 'errors' => $error], 422)
            
        }
    }









}
?>