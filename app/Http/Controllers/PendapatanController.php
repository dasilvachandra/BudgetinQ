<?php

namespace App\Http\Controllers;
use Validator;
use App\Pendapatan;
use App\Transaksi;
use App\JenisPendapatan;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Auth;

class PendapatanController extends Controller
{
    public function store(Request $request)
    {
        $pendapatan = new Pendapatan;
        $transaksi = new Transaksi;
        $jenis_pendapatan = new JenisPendapatan;

        $rules = array(
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'nama_pendapatan' => 'required|min:2|max:255',
            'jenis_pendapatan' => 'required|max:100',
        );
        $customMessages = [
            'nama_pendapatan.required' => 'Deskripsi Pemasukkan masih kosong',
            'jumlah.required' => 'Jumlah dana masih kosong',
            'jenis_pendapatan.required' => 'Kategori masih kosong',
        ];
        // dd($request);
        $validator = $this->validate($request, $rules, $customMessages);

        $picture_name = null;

        $id_user=Auth::user()->id;
        $email=Auth::user()->email;  
        $id_pendapatan = 'PEND_'.uniqid();
        $id_transaksi = 'TR_'.uniqid();
        $id_jenis_pendapatan = $validator['jenis_pendapatan'];

        $time = $this->dateFilter($validator['time']);
        $time = str_replace(',', ' ', $time);
        $time = date("Y-m-d", strtotime($time));  
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $nama_pendapatan = $validator['nama_pendapatan'];
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $validator['jumlah']));
        dd($validator);
    }
    
    public function destroy(Request $request)
    {
        $rules = array(
            'id' => 'required|max:255'
        );
        $customMessages = [
            'id.required' => 'id failed'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $id = $validator['id'];
        $pendapatan = new Pendapatan;
        $pendapatanByID = $pendapatan->selectByID($id);
        if (count($pendapatanByID)==0) {
            $time = date("d F, Y"); 
            $data=$this->dataDefault($time,$time);
            return back()->with($data);
        }
        $id_user=Auth::user()->id; 
        $email=Auth::user()->email; 
        // dd($id);
        $time = date("d F, Y", strtotime($pendapatanByID[0]->waktu)); 
        DB::select('
            DELETE pendapatan, transaksi 
            from pendapatan 
            inner join transaksi on id_pendapatan = jenis_transaksi 
            inner join users using (id) 
            where id_pendapatan = ? and id=? and email = ?;
            ', [$id,$id_user,$email]
        );
        $dataPendapatan=$pendapatan->selectAll($time);
        $time = $this->dateFilter($time);
        $time = str_replace(',', ' ', $time);
        $time = date("F, Y", strtotime($time));  
        $danamasuk = $this->danamasuk($time);
        $data=array(
            'pendapatan' => $dataPendapatan,
            'time' => $time,
            'danamasuk' => $danamasuk,
        );
        return response()->json($data);
    }

    public function edit($id)
    {
        if (substr($id, 0,1) == '"' && substr($id, strlen($id)-1,strlen($id)) == '"' ) {
            $id = explode('"', $id)[1];
        }
        $data = DB::select('select * from pendapatan inner join transaksi on jenis_transaksi=id_pendapatan where id_pendapatan = ?', [$id]);
        //dd($data);
        return response()->json($data);

    }

    // public function dataPendapatan(Request $request){

    //     $rules = array(
    //         'time' => 'required|max:255'
    //     );
    //     $customMessages = [
    //         'time.required' => 'time error'
    //     ];
    //     $validator = $this->validate($request, $rules, $customMessages);
        
    //     $time = $validator['time'];
    //     $pendapatan = new Pendapatan;
    //     $data = array(
    //         'pendapatan'=> $pendapatan->selectAll($time);
    //     );
    //     return $data;
    // }

    // public function apiPendapatan($time="")
    // {
    //     // dd($time);
    //     if ($time="") date("Y-m-d");

    //     $pendapatan = new Pendapatan;
    //     $d=$this->timeByMonth($time);
    //     $start_default = $d['start_default'];
    //     $end_default = $d['end_default'];
    //     $dataPendapatan=$pendapatan->selectRange($start_default,$end_default);
    //     // dd($dataPendapatan);
    //     return datatables($dataPendapatan)->toJson();
    // }
    // public function apiPendapatanRangeDay($time)
    // {
    //     $pendapatan = new Pendapatan;
    //     $d=$this->timeByMonth($time);
    //     $data=$pendapatan->selectRange($d['start_default'],$d['end_default']);
    //     return datatables($data)->toJson();
    // }
    // public function specifydate($time)
    // {
    //     $data=$this->dataDefault($time,$time);
    //     $data['view']='specifydate';
    //     return view('app_keuangan.pendapatan.index')->with($data);
    // }
    // public function show_range($time)
    // {
    //     $data=$this->dataDefault($time);
    //     $data['view']='rangedate';
    //     return view('app_keuangan.pendapatan.index')->with($data);
    // }

    // public function create()
    // {
    //     $data=$this->dataDefault(date("d F, Y"));
    //     $data['view']='Create';
    //     return view('app_keuangan.pendapatan.index')->with($data);
    // }

    // public function show($waktu)
    // {
    //     $id_user=Auth::user()->id;
    //     $email=Auth::user()->email;
    //     $data=$this->dataDefault($waktu);
    //     return view('app_keuangan.pendapatan.table')->with($data);
    // }

    // public function showInfo($id)
    // {
    //     // dd($id);
    //     if (substr($id, 0,1) == '"' && substr($id, strlen($id)-1,strlen($id)) == '"' ) {
    //         $id = explode('"', $id)[1];
    //     }
    //     $data = DB::select('select * from pendapatan inner join transaksi on jenis_transaksi=id_pendapatan where id_pendapatan = ?', [$id]);
    //     return response()->json($data);
    // }

    public function update(Request $request)
    {
        $rules = array(
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'id_pendapatan' => 'required|exists:pendapatan|max:255',
            'nama_pendapatan' => 'required|min:2|max:255',
            'jenis_pendapatan' => 'required|max:100',
        );
        $customMessages = [
            'id_pendapatan.exists' => 'ID Penedapatan not match',
            'price.required' => 'Jumlah dana masih kosong',
            'nama_pendapatan.required' => 'Nama pengeluaran masih kosong',
            'jenis_pendapatan.required' => 'Kategori masih kosong',
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        
        $id_pendapatan = $request->id_pendapatan;
        $nama_pendapatan = $request->nama_pendapatan;
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $validator['jumlah']));
        // dd($jumlah);
        $jenis_pendapatan = $request->jenis_pendapatan;
        $time = $request->time;
        $date = str_replace(',', ' ', $time);
        $waktu = date("Y-m-d", strtotime($date)); 

        $id_user=Auth::user()->id;
        $data_pendapatan_arr = [$nama_pendapatan,$jumlah,$jenis_pendapatan,$id_pendapatan];
        $data_transaksi_arr = [$waktu,$id_pendapatan,$id_user];
        DB::select('UPDATE pendapatan set nama_pendapatan = ?, jumlah = ? , id_jenis_pendapatan = ? WHERE id_pendapatan = ?', $data_pendapatan_arr);
        DB::select('UPDATE transaksi set waktu = ? WHERE jenis_transaksi = ? and id = ? ', $data_transaksi_arr);
       
        // $danamasuk = $this->danamasuk($request->time);

        $time = $this->dateFilter($validator['time']);
        $time = str_replace(',', ' ', $time);
        $time = date("F, Y", strtotime($time));
        $pendapatan = new Pendapatan;
        $dataPendapatan=$pendapatan->selectAll($time);  

        $danamasuk = $this->danamasuk($time);
        $data=array(
            'time' => $time,
            'jumlah' => $jumlah,
            'id_pendapatan' => $id_pendapatan,
            'nama_pendapatan' => $nama_pendapatan,
            'jenis_pendapatan' => $jenis_pendapatan,
            'danamasuk' => $danamasuk,
            'pendapatan' => $dataPendapatan
        );

        return response()->json($data);

    }
}
