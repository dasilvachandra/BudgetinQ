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
        $id_user=Auth::user()->id;
        $pengeluaran = new Pengeluaran;
        $transaksi = new Transaksi;
        $jenis_pengeluaran = new JenisPengeluaran;
        // dd($request);

        $rules = array(
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
            'time' => 'required|max:255',
            'jumlah' => 'required|max:255',
            'nama_pengeluaran' => 'required|min:2|max:255',
            'jenis_pengeluaran' => 'required|max:100',
        );
        // dd($request);
        $customMessages = [
            'price.required' => 'Jumlah dana masih kosong',
            'nama_pengeluaran.required' => 'Nama pengeluaran masih kosong',
            'jenis_pengeluaran.required' => 'Kategori masih kosong',
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $picture_name = null;
        if ($request->picture != null) {
            $image = $request->file('picture');
            // $fileName   = date('ymdhis').'.'.$image->getClientOriginalName();
            $temp = explode(".", $image->getClientOriginalName());

            $picture_name = $request->id_pengeluaran.'-'.base64_encode(round(microtime(true)) . '.' . end($temp)).'.jpg';
            $destination_path = $_SERVER['DOCUMENT_ROOT'].'/front_end/images/upload_pengeluaran/';
            $image->move($destination_path,$picture_name);
            // dd($destination_path);
        }

        $id_jenis_pengeluaran = $validator['jenis_pengeluaran'];

        if (count($jenis_pengeluaran->selectByID($validator['jenis_pengeluaran']))==0 && count($jenis_pengeluaran->selectByName($validator['jenis_pengeluaran']))==0) {
            if (count($jenis_pengeluaran->selectAll())>=10) {
                return redirect()->to("/")->withInput($request->input()); 
            }else{
                $id_jenis_pengeluaran = 'JPG_'.uniqid();
                $dataJenisPengeluaran = array($id_jenis_pengeluaran, $request->jenis_pengeluaran, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"),$id_user);
                $jenis_pengeluaran->insertData($dataJenisPengeluaran);
            }
        }

        $id_pengeluaran = 'PENG_'.uniqid();
        $nama_pengeluaran = $request->nama_pengeluaran;
        $jumlah = intval(preg_replace('/[^0-9]+/', '', $request->jumlah));
        $picture= $picture_name;
        

        $dataPengeluaran = array($id_pengeluaran,$nama_pengeluaran,$jumlah,$picture,$id_jenis_pengeluaran);
        DB::select('INSERT INTO pengeluaran (id_pengeluaran, nama_pengeluaran, jumlah,picture,id_jenis_pengeluaran) VALUES (?, ?, ?, ?, ?)', $dataPengeluaran);

        $id_transaksi = 'TR_'.uniqid();
        $jenis_transaksi = $id_pengeluaran;
        $waktu = date("Y-m-d", strtotime($this->dateFilter($validator['time'])));
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $id = $id_user;

        $dataTransaksi = array($id_transaksi,$jenis_transaksi,$waktu,$created_at,$updated_at,$id);
        DB::select('INSERT INTO transaksi (id_transaksi, jenis_transaksi, waktu,created_at,updated_at,id) VALUES (?, ?, ?, ?, ?,?)', $dataTransaksi);
        $d=$this->timeByMonth($validator['time']);
        // $start_default = $d['start_default'];
        $end_default = $d['end_default'];
        $data=$this->dataDefault($validator['time']);
        $total_per_hari = $pengeluaran->totalByDate($end_default,$end_default)[0]->total_per_hari;
        $data['total_per_hari'] = $total_per_hari;
        $data['pengeluaranHariIni'] = $pengeluaran->selectRange($end_default,$end_default);
        $data['time'] = $validator['time'];
        return response()->json($data);
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
        $pengeluaran = new Pengeluaran;
        $pengeluaranByID = $pengeluaran->selectByID($id);
        if (count($pengeluaranByID)==0) {
            $time = date("d F, Y"); 
            $data=$this->dataDefault($time,$time);
            return back()->with($data);
        }
        $id_user=Auth::user()->id; 
        $email=Auth::user()->email; 
        // dd($id);
        $time = date("d F, Y", strtotime($pengeluaranByID[0]->waktu)); 
        DB::select('
            DELETE pengeluaran, transaksi 
            from pengeluaran 
            inner join transaksi on id_pengeluaran = jenis_transaksi 
            inner join users using (id) 
            where id_pengeluaran = ? and id=? and email = ?;
            ', [$id,$id_user,$email]
        );

        
        $end_default =  $pengeluaranByID[0]->waktu;
        // $d=$this->timeByMonth($validator['time']);
        // $start_default = $d['start_default'];
        $data=$this->dataDefault($time);
        $data['total_per_hari'] = $pengeluaran->totalByDate($end_default,$end_default)[0]->total_per_hari;
        $data['pengeluaranHariIni'] = $pengeluaran->selectRange($end_default,$end_default);
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

    public function edit($id)
    {
        $pengeluaran = new Pengeluaran;
        $id_user=Auth::user()->id;
        // $data = DB::select('select * from pengeluaran inner join transaksi on jenis_transaksi=id_pengeluaran where id_pengeluaran = ?', [$id]);        $d=$this->timeByMonth($data[0]->waktu);
        $editData = $pengeluaran->selectByID($id);
        $time = date("d F, Y", strtotime($editData[0]->waktu)); 
        $d=$this->timeByMonth($time);
        $start_default = $d['start_default'];
        $end_default = $d['end_default'];
        $dataPengeluaran=$pengeluaran->selectRange($end_default,$end_default);
        $data=$this->dataDefault($time);
        $data['time']=$time;
        $data['editData']=$pengeluaran->selectByID($id);
        $data['pengeluaran']=$dataPengeluaran;
        return $data;
    }

    public function update(Request $request)
    {
        // dd($request);
        // dd($request->id_jenis_pengeluaran);
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
        $picture_name = null;
        $id_pengeluaran=$request->id_pengeluaran;
        $pengeluaran = new Pengeluaran;
        $pengeluaranByID = $pengeluaran->selectByID($id_pengeluaran);
        $picture_name = $pengeluaranByID[0]->picture;
        if ($picture_name==null) {
            $picture_name = "noimage.png";
        }

        if ($request->picture != null ) {
            $destination_path = $_SERVER['DOCUMENT_ROOT'].'/front_end/images/upload_pengeluaran/';
            if(file_exists($destination_path.$picture_name) && $picture_name != "noimage.png"){
                unlink($destination_path.$picture_name);
            }
            $image = $request->file('picture');
            // $fileName   = date('ymdhis').'.'.$image->getClientOriginalName();
            $temp = explode(".", $image->getClientOriginalName());
            $picture_name = $request->id_pengeluaran.'-'.base64_encode(round(microtime(true)) . '.' . end($temp)).'.jpg';
            // $image->move(public_path('/front_end/images/upload_pengeluaran/'),$picture_name);
            $image->move($destination_path,$picture_name);
            
        }

        $waktu = date("Y-m-d", strtotime($this->dateFilter($validator['time'])));
        $jumlah = $this->number($validator['jumlah']); 
        $nama_pengeluaran = $validator['nama_pengeluaran'];
        $id_jenis_pengeluaran = $validator['id_jenis_pengeluaran'];
        $id=Auth::user()->id;

        $dataPengeluaran = array(
            $nama_pengeluaran,
            $jumlah,
            $picture_name,
            $id_jenis_pengeluaran,
            $id_pengeluaran
        );

        $dataTransaksi = array(
            $waktu,
            $id_pengeluaran,
            $id
        );
        // dd($dataPengeluaran);

        DB::select('UPDATE pengeluaran set nama_pengeluaran = ?, jumlah = ? , picture = ?, id_jenis_pengeluaran = ? WHERE id_pengeluaran = ?', $dataPengeluaran);
        DB::select('UPDATE transaksi set waktu = ? WHERE jenis_transaksi = ? and id = ? ', $dataTransaksi);
       
        $time = $this->dateFilter($validator['time']);

        $data=$this->dataDefault($time);
        $d=$this->timeByMonth($time);
        $start_default = $d['start_default'];
        $end_default = $d['end_default'];
        $data['time'] = $validator['time'];
        $data['pengeluaranHariIni'] = $pengeluaran->selectRange($end_default,$end_default);
        $data['total_per_hari'] = $pengeluaran->totalByDate($end_default,$end_default)[0]->total_per_hari;
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
