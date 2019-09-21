<?php

namespace App\Http\Controllers;
use Validator;
use App\Pengeluaran;
use App\Transaksi;
use App\JenisPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Auth;

class PengeluaranController extends Controller
{
    public function dataPerHari(Request $request){
        $rules = array(
            'time' => 'required|max:255'
        );
        $customMessages = [
            'time.required' => 'time error'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $d=$this->timeByMonth($validator['time']);
        $end_default = $d['end_default'];
        $pengeluaran = new Pengeluaran;
        $total_per_hari = $pengeluaran->totalByDate($end_default,$end_default);
        $data = array(
            'time' => $validator['time'],
            'pengeluaranHariIni'=> $pengeluaran->selectRange($end_default,$end_default),
            'total_per_hari' => $total_per_hari[0]->total_per_hari
        );
        return $data;
    }
    public function store(Request $request)
    {
        $id=Auth::user()->id;
        $pengeluaran = new Pengeluaran;
        $transaksi = new Transaksi;
        $jenis_pengeluaran = new JenisPengeluaran;
        // dd($request);

        $rules = array(
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'nama_pengeluaran' => 'required|min:2|max:255',
            'id_jenis_pengeluaran' => 'required|max:100',
        );
        // dd($request);
        $customMessages = [
            'price.required' => 'Jumlah dana masih kosong',
            'nama_pengeluaran.required' => 'Deskripsi pengeluaran masih kosong',
            'id_jenis_pengeluaran.required' => 'Kategori masih kosong',
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $id_pengeluaran = 'PENG_'.base_convert(microtime(false), 10, 36); 
        $nama_pengeluaran = $validator['nama_pengeluaran'];
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $validator['jumlah']));
        $picture = "";
        $id_jenis_pengeluaran = $validator['id_jenis_pengeluaran'];

        // select nama_pengeluaran, id_jenis_pengeluaran, waktu,jumlah from transaksi inner join pengeluaran on jenis_transaksi=id_pengeluaran where id=1;
        $id_transaksi = 'TR_'.base_convert(microtime(false), 10, 36); 
        $jenis_transaksi = $id_pengeluaran;
        $waktu = date("Y-m-d", strtotime($this->dateFilter($validator['time'])));
        $checkDuplicate=$pengeluaran->checkDuplicate([$id,$waktu,$nama_pengeluaran,$jumlah,$id_jenis_pengeluaran]);
        $link = '/danakeluar/'.date("F, Y", strtotime($waktu)).'/'.date("d", strtotime($waktu));
        if (count($checkDuplicate)==0) {
            $pengeluaran->insert([$id_pengeluaran, $nama_pengeluaran, $jumlah,$picture,$id_jenis_pengeluaran]);
            $transaksi->insert([$id_transaksi, $jenis_transaksi, $waktu,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$id]);
            $data = array(
                'pesan' => '1',
                'link' => $link,
                'list_pengeluaran' => $pengeluaran->selectAll($this->timeByMonth($waktu))
            );
            return response()->json($data);
        }else{
            return response()->json(['errors' => ['Data sudah ada di hari yang sama']], 422);
        }
        
        // return redirect()->to('/danakeluar');
    }

    public function edit(Request $request)
    {
        $rules = array(
            'id_pengeluaran' => 'required|exists:pengeluaran|max:255',
            
        );
        $customMessages = [
            'id_pengeluaran.exists' => 'ID Pengeluaran not match',
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $pengeluaran = new Pengeluaran;
        $id_user=Auth::user()->id;
        // $data = DB::select('select * from pengeluaran inner join transaksi on jenis_transaksi=id_pengeluaran where id_pengeluaran = ?', [$id]);        $d=$this->timeByMonth($data[0]->waktu);
        $editData = $pengeluaran->selectByID($validator['id_pengeluaran']);
        // dd($editData);
        $data = array(
            'editData' => $editData
        );
        return response()->json($data);
    }


    public function delete(Request $request)
    {
        $pengeluaran = new Pengeluaran;
        $id_user=Auth::user()->id; 
        $email=Auth::user()->email; 
        $rules = array(
            'id' => 'required|max:255'
        );
        $customMessages = [
            'id.required' => 'id failed'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        
        $pengeluaran->remove([$validator['id'],$id_user,$email]);
        $data = array(
            'pesan' => '0',
        );
        return response()->json($data);
    }

    public function dataPerBulan(Request $request){
        $rules = array(
            'time' => 'required|max:255'
        );
        $customMessages = [
            'time.required' => 'time error'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $time = $validator['time'];
        $pengeluaran = new Pengeluaran;
        $data = array(
            'pengeluaran'=> $pengeluaran->selectAll($time),
        );
        return $data;
    }

    public function update(Request $request)
    {
        // dd($request);
        // dd($request->id_pengeluaran);
        $pengeluaran = new Pengeluaran;
        $transaksi = new Transaksi;
        $id=Auth::user()->id;
        $rules = array(
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'id_pengeluaran' => 'required|exists:pengeluaran|max:255',
            'nama_pengeluaran' => 'required|min:2|max:255',
            'id_jenis_pengeluaran' => 'required|exists:jenis_pengeluaran|max:100',
        );
        $customMessages = [
            'id_pengeluaran.exists' => 'ID Pengeluaran not match',
            'price.required' => 'Jumlah dana masih kosong',
            'nama_pengeluaran.required' => 'Nama pengeluaran masih kosong',
            'id_jenis_pengeluaran.required' => 'Kategori masih kosong',
        ];

        $validator = $this->validate($request, $rules, $customMessages);
        $id_pengeluaran = $validator['id_pengeluaran'];
        $nama_pengeluaran = $validator['nama_pengeluaran'];
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $validator['jumlah']));
        $picture = "";
        $id_jenis_pengeluaran = $validator['id_jenis_pengeluaran'];

        $id_transaksi = DB::table('transaksi')->where('jenis_transaksi', $id_pengeluaran)->first()->id_transaksi;
        $jenis_transaksi = $id_pengeluaran;
        $waktu = date("Y-m-d", strtotime($this->dateFilter($validator['time'])));
        $pengeluaran->patch([$nama_pengeluaran, $jumlah,$picture,$id_jenis_pengeluaran,$id_pengeluaran]);
        $transaksi->patch([$waktu, $jenis_transaksi, $id]);
        $link = '/danakeluar/'.date("F, Y", strtotime($waktu)).'/'.date("d", strtotime($waktu));
        $data = array(
            'pesan' => '1',
            'link' => $link,
            'list_pengeluaran' => $pengeluaran->selectAll($this->timeByMonth($waktu))
        );
        return response()->json($data);

    }

    public function deletePicture($id,$picture){

        $id_user=Auth::user()->id; 
        $pengeluaran = new Pengeluaran;
        $pengeluaranByID = $pengeluaran->selectByID($id);
        $filename = $pengeluaranByID[0]->picture;
        // dd($pengeluaranByID);
        $destination_path = $_SERVER['DOCUMENT_ROOT'].'/front_end/images/upload_pengeluaran/';
        
        DB::select('UPDATE pengeluaran inner join transaksi on jenis_transaksi=id_pengeluaran set picture = NULL WHERE id_pengeluaran = ? and id = ?', [$id, $id_user] );
        if ($filename==null) {
            $filename = "noimage.png";
        }


        if(file_exists($destination_path.$filename) && $filename != "noimage.png"){
            unlink($destination_path.$filename);
        }
        return response()->json("Picture has removed");
    }

}
