<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisPengeluaran;
use App\JenisPendapatan;
use App\Pendapatan;
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
        $rules = array(
            'jenis_pengeluaran' => [ 'required', 
                Rule::unique('jenis_pengeluaran')->where(function ($query) {
                    $id=Auth::user()->id;
                    $query->where('id', $id);
                }),
            ],
            'group_category_id' => 'required'
        );
        $customMessages = [
            'jenis_pengeluaran.required' => 'Jenis Pengeluaran invalid',
            'group_category_id.required' => 'Group Category invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $dataGC = DB::table("group_category")->where('group_category_id',$validator['group_category_id'])->get();
        if (count($dataGC)==0) {
            return response()->json(['errors' => ['group kategori gak ada!!']], 422);
        }

        $data = array(
            'KTG_'.uniqid(),
            $validator['jenis_pengeluaran'],
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
        $dataKatByID = DB::select('SELECT * from jenis_pengeluaran where id_jenis_pengeluaran = ? limit 1',[$validator['id_jenis_pengeluaran']]); 

        $data = array(
            'dataKatByID' => $dataKatByID,
            'all_jenis_pengeluaran' => $this->selectAll(),
        );

        return $data;
    }

    public function update(Request $request)
    {
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $id_user=Auth::user()->id;
        $rules = array(
            'id_jenis_pengeluaran' => 'required|exists:jenis_pengeluaran',
            'jenis_pengeluaran' => 'required',
            'group_category_id' => 'required'
        );
        // dd($request);
        $customMessages = [
            'id_jenis_pengeluaran.required' => 'ID Jenis Pengeluaran invalid',
            'group_category_id.required' => 'Group Category invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $jenisPengeluaranBefore = $katPengeluaran->selectByID($validator['id_jenis_pengeluaran']);

        #filter 1
        $filter1 = DB::select("select jenis_pengeluaran from jenis_pengeluaran where id=1 and id_jenis_pengeluaran != ? and jenis_pengeluaran = ? ",[$validator['id_jenis_pengeluaran'],$validator['jenis_pengeluaran']]);
        if (count($filter1)>=1) {
            return response()->json(['errors' => ["Kategori ini sudah ada"]], 422);
        }
        // filter 2 : check id group_category yang ada di database.
        $filter2 = DB::select("select * from group_category where group_category_id = ?",[$validator['group_category_id']]);
        if (count($filter2)==0) {
            return response()->json(['errors' => ['Value group category tidak boleh diubah']], 422);
        }



       
        // check group category gabung antara jenis pengeluaran dan pendapatan
        $filter3 = DB::select("select gabung from group_category where group_category_id = ?",[$validator['group_category_id']])[0]->gabung;
        $filter4 = DB::select("select gabung from group_category where group_category_id = ?",[$jenisPengeluaranBefore[0]->group_category_id])[0]->gabung;
        
        if ($filter3!=$filter4 ) {
            return response()->json(['errors' => ["UPDATE Gagal karena Group Category Tidak Cocok"]], 422);
        }

        if ($filter3==$filter4) {
            $data=[$validator['jenis_pengeluaran'],$validator['group_category_id'],$jenisPengeluaranBefore[0]->jenis_pengeluaran,$id_user,$jenisPengeluaranBefore[0]->group_category_id];
            $katPendapatan->updateByName($data);
        }
        $data=[$validator['jenis_pengeluaran'],$validator['group_category_id'],$validator['id_jenis_pengeluaran'],$id_user];
        $katPengeluaran->updateByID($data);
        return $this->selectAll(); 
    }

    public function delete(Request $request)
    {
        $katPengeluaran = new JenisPengeluaran;
        $katPendapatan = new JenisPendapatan;
        $pendapatan = new Pendapatan;
        $id_user=Auth::user()->id; 
        
        $rules = array(
            'id_jenis_pengeluaran' => [ 'required', 
                Rule::exists('jenis_pengeluaran')->where(function ($query) {
                    $id=Auth::user()->id;
                    $query->where('id', $id);
                }),
            ],

        );
        $customMessages = [
            'id_jenis_pengeluaran.required' => 'ID Jenis Pengeluaran invalid'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        
        $checkTransaksi = DB::table("jenis_pengeluaran")
                                    ->join('pengeluaran','jenis_pengeluaran.id_jenis_pengeluaran','=','pengeluaran.id_jenis_pengeluaran')
                                    ->where([
                                        ['jenis_pengeluaran.id_jenis_pengeluaran',$validator['id_jenis_pengeluaran']],
                                        ['id',$id_user],
                                    ])
                                    ->get();
        if (count($checkTransaksi)>=1) {
            $jumlah = count($checkTransaksi);
            $url = "/danakeluar/kategori/{$validator['id_jenis_pengeluaran']}/";
            $pesan = "terdapat {$jumlah} data menggunakan kategori ini <a href={$url} class='alert-link'>Lihat data</a>";
            $error = [ 'id_jenis_pengeluaran' => $pesan];
            return response()->json(['message' => 'data yang dikirimkan terfilter', 'errors' => $error], 422);
        }if(count($checkTransaksi)==0){
            $checkGroupCategory = DB::table("jenis_pengeluaran")
                                    ->join('group_category','jenis_pengeluaran.group_category_id','=','group_category.group_category_id')
                                    ->where([
                                        ['jenis_pengeluaran.id_jenis_pengeluaran',$validator['id_jenis_pengeluaran']],
                                        ['id',$id_user],
                                    ])
                                    ->get();
            $dataPengeluaran = [$validator['id_jenis_pengeluaran'],$id_user];
            if ($checkGroupCategory[0]->pengeluaran==1) {
                $katPengeluaran->deleteByID($dataPengeluaran);
            }
            if ($checkGroupCategory[0]->gabung==1) {
                $katPendapatan->deleteByID($dataPengeluaran);
            }
            return [
                'url'=>'/kategori/danakeluar',
            ];
        
            
            
        }

        

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