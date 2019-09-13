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
        $rules = array(
            'jenis_pendapatan' => [ 'required', 
                Rule::unique('jenis_pendapatan')->where(function ($query) {
                    $id=Auth::user()->id;
                    $query->where('id', $id);
                }),
            ],
            'group_category_id' => 'required'
        );
        $customMessages = [
            'jenis_pendapatan.required' => 'Jenis Pendapatan invalid',
            'group_category_id.required' => 'Group Category invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
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
            Auth::user()->id,
            $this->randomRGB()
        );

        $katPengeluaran->insertData($data);
        if ($dataGC[0]->gabung==1) {
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
        $checkJenisPengeluaranBefore = DB::table("jenis_pendapatan")
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
            $katPengeluaran->updateByID($dataKategori);
            if($checkJenisPengeluaranBefore[0]->gabung==1){
                $checkTransaksi = $pengeluaran->selectByIDJPG($validator['id_jenis_pendapatan']);
                if (count($checkTransaksi)==0) {
                    $katPendapatan->deleteByID([$validator['id_jenis_pendapatan'],$id_user]);
                }else{
                    return response()->json(['errors' => [$checkTransaksi[0]->jenis_pendapatan." Masih terikat dengan ".count($checkTransaksi)." data danamasuk"]], 422);                }
                
            }
        }

        if ($checkGroupCategory[0]->gabung==1) {
            $katPengeluaran->updateByID($dataKategori);
            if($checkJenisPengeluaranBefore[0]->gabung==0){
                $dataInsert = array(
                    $validator['id_jenis_pendapatan'],
                    $validator['jenis_pendapatan'],
                    $validator['group_category_id'],
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                    $id_user,
                    $this->randomRGB()
                );
                $katPendapatan->insertData($dataInsert);
            }
            $katPendapatan->updateByID($dataKategori);
        }

        return [
            'url'=>'/kategori/danamasuk',
        ];

    }

    public function delete(Request $request)
    {
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
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
        $checkTransaksi = $pengeluaran->selectByIDJPG($validator['id_jenis_pendapatan']);

        if (count($checkTransaksi)>=1) {
            $url="<a href='/danamasuk/kategori/".$validator['id_jenis_pendapatan']."/'>Lihat Data</a>";
            return response()->json(['errors' => [$checkTransaksi[0]->jenis_pendapatan." Masih terikat dengan ".count($checkTransaksi)." data danamasuk. $url"]], 422);
        }if(count($checkTransaksi)==0){
            $checkGroupCategory = DB::table("jenis_pendapatan")
                                    ->join('group_category','jenis_pendapatan.group_category_id','=','group_category.group_category_id')
                                    ->where([
                                        ['jenis_pendapatan.id_jenis_pendapatan',$validator['id_jenis_pendapatan']],
                                        ['id',$id_user],
                                    ])
                                    ->get();
            $dataPengeluaran = [$validator['id_jenis_pendapatan'],$id_user];
            if ($checkGroupCategory[0]->pengeluaran==1) {
                $katPengeluaran->deleteByID($dataPengeluaran);
            }
            if ($checkGroupCategory[0]->gabung==1) {
                $katPendapatan->deleteByID($dataPengeluaran);
            }
            
            return [
                'url'=>'/kategori/danamasuk',
            ];
        
            
            
        }

        

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
        $dataPengeluaranByKat = DB::select('SELECT * from pengeluaran inner join transaksi on id_pengeluaran = jenis_transaksi where id_jenis_pendapatan = ? ',[$validator['id_jenis_pendapatan']]); 
        
        $dataKatByID = DB::select('SELECT * from jenis_pendapatan where id_jenis_pendapatan = ? limit 1',[$validator['id_jenis_pendapatan']]); 

        $jumlah = count($dataPengeluaranByKat);
        if ($jumlah>0) {
            $data = array(
                'dataKatByID' => $dataKatByID,
                'dataPengeluaranByKat' => $dataPengeluaranByKat,
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