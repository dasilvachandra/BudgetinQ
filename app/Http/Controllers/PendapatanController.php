<?php

namespace App\Http\Controllers;
use Validator;
use App\Pendapatan;
use App\Transaksi;
use App\JenisPendapatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Auth;

class PendapatanController extends Controller
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
        $pendapatan = new Pendapatan;
        $total_per_hari = $pendapatan->totalByDate($end_default,$end_default);
        $data = array(
            'time' => $validator['time'],
            'pendapatanHariIni'=> $pendapatan->selectRange($end_default,$end_default),
            'total_per_hari' => $total_per_hari[0]->total_per_hari
        );
        return $data;
    }
    public function store(Request $request)
    {
        $id=Auth::user()->id;
        $pendapatan = new Pendapatan;
        $transaksi = new Transaksi;
        $jenis_pendapatan = new JenisPendapatan;
        // dd($request);

        $rules = array(
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'nama_pendapatan' => 'required|min:2|max:255',
            'id_jenis_pendapatan' => 'required|max:100',
        );
        // dd($request);
        $customMessages = [
            'price.required' => 'Jumlah dana masih kosong',
            'nama_pendapatan.required' => 'Deskripsi pendapatan masih kosong',
            'id_jenis_pendapatan.required' => 'Kategori masih kosong',
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $id_pendapatan = 'PENG_'.uniqid();
        $nama_pendapatan = $validator['nama_pendapatan'];
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $validator['jumlah']));
        $picture = "";
        $id_jenis_pendapatan = $validator['id_jenis_pendapatan'];

        // select nama_pendapatan, id_jenis_pendapatan, waktu,jumlah from transaksi inner join pendapatan on jenis_transaksi=id_pendapatan where id=1;
        $id_transaksi = "TR_".uniqid();
        $jenis_transaksi = $id_pendapatan;
        $waktu = date("Y-m-d", strtotime($this->dateFilter($validator['time'])));
        $checkDuplicate=$pendapatan->checkDuplicate([$id,$waktu,$nama_pendapatan,$jumlah,$id_jenis_pendapatan]);
        $link = '/danamasuk/'.date("F, Y", strtotime($waktu)).'/'.date("d", strtotime($waktu));
        if (count($checkDuplicate)==0) {
            $pendapatan->insert([$id_pendapatan, $nama_pendapatan, $jumlah,$picture,$id_jenis_pendapatan]);
            $transaksi->insert([$id_transaksi, $jenis_transaksi, $waktu,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$id]);
            $data = array(
                'pesan' => '1',
                'link' => $link,
                'list_pendapatan' => $pendapatan->selectAll($this->timeByMonth($waktu))
            );
            return response()->json($data);
        }else{
            return response()->json(['errors' => ['Data sudah ada']], 422);
        }
        
        // return redirect()->to('/danamasuk');
    }

    public function edit(Request $request)
    {
        $rules = array(
            'id_pendapatan' => 'required|exists:pendapatan|max:255',
            
        );
        $customMessages = [
            'id_pendapatan.exists' => 'ID Pendapatan not match',
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $pendapatan = new Pendapatan;
        $id_user=Auth::user()->id;
        // $data = DB::select('select * from pendapatan inner join transaksi on jenis_transaksi=id_pendapatan where id_pendapatan = ?', [$id]);        $d=$this->timeByMonth($data[0]->waktu);
        $editData = $pendapatan->selectByID($validator['id_pendapatan']);
        // dd($editData);
        $data = array(
            'editData' => $editData
        );
        return response()->json($data);
    }


    public function delete(Request $request)
    {
        $pendapatan = new Pendapatan;
        $id_user=Auth::user()->id; 
        $email=Auth::user()->email; 
        $rules = array(
            'id' => 'required|max:255'
        );
        $customMessages = [
            'id.required' => 'id failed'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        
        $pendapatan->remove([$validator['id'],$id_user,$email]);
        $data = array(
            'pesan' => '1',
            'link' =>'/danamasuk',
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
        $pendapatan = new Pendapatan;
        $data = array(
            'pendapatan'=> $pendapatan->selectAll($time),
        );
        return $data;
    }

    public function update(Request $request)
    {
        // dd($request);
        // dd($request->id_pendapatan);
        $pendapatan = new Pendapatan;
        $transaksi = new Transaksi;
        $id=Auth::user()->id;
        $rules = array(
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'id_pendapatan' => 'required|exists:pendapatan|max:255',
            'nama_pendapatan' => 'required|min:2|max:255',
            'id_jenis_pendapatan' => 'required|exists:jenis_pendapatan|max:100',
        );
        $customMessages = [
            'id_pendapatan.exists' => 'ID Pendapatan not match',
            'price.required' => 'Jumlah dana masih kosong',
            'nama_pendapatan.required' => 'Nama pendapatan masih kosong',
            'id_jenis_pendapatan.required' => 'Kategori masih kosong',
        ];

        $validator = $this->validate($request, $rules, $customMessages);
        $id_pendapatan = $validator['id_pendapatan'];
        $nama_pendapatan = $validator['nama_pendapatan'];
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $validator['jumlah']));
        $picture = "";
        $id_jenis_pendapatan = $validator['id_jenis_pendapatan'];

        $id_transaksi = DB::table('transaksi')->where('jenis_transaksi', $id_pendapatan)->first()->id_transaksi;
        $jenis_transaksi = $id_pendapatan;
        $waktu = date("Y-m-d", strtotime($this->dateFilter($validator['time'])));
        $pendapatan->patch([$nama_pendapatan, $jumlah,$picture,$id_jenis_pendapatan,$id_pendapatan]);
        $transaksi->patch([$waktu, $jenis_transaksi, $id]);
        $link = '/danamasuk/'.date("F, Y", strtotime($waktu)).'/'.date("d", strtotime($waktu));
        $data = array(
            'pesan' => '1',
            'link' => $link,
            'list_pendapatan' => $pendapatan->selectAll($this->timeByMonth($waktu))
        );
        return response()->json($data);

    }

    public function deletePicture($id,$picture){

        $id_user=Auth::user()->id; 
        $pendapatan = new Pendapatan;
        $pendapatanByID = $pendapatan->selectByID($id);
        $filename = $pendapatanByID[0]->picture;
        // dd($pendapatanByID);
        $destination_path = $_SERVER['DOCUMENT_ROOT'].'/front_end/images/upload_pendapatan/';
        
        DB::select('UPDATE pendapatan inner join transaksi on jenis_transaksi=id_pendapatan set picture = NULL WHERE id_pendapatan = ? and id = ?', [$id, $id_user] );
        if ($filename==null) {
            $filename = "noimage.png";
        }


        if(file_exists($destination_path.$filename) && $filename != "noimage.png"){
            unlink($destination_path.$filename);
        }
        return response()->json("Picture has removed");
    }

}
