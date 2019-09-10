<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Pendapatan;
use App\Pengeluaran;
use App\Transaksi;
use App\JenisPengeluaran;
class BudgetinQController  extends Controller
{
    public $date;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard($time=null)
    {
        $time = $time ?: date("F, Y");
        $pengeluaran = new Pengeluaran;
        $pendapatan = new Pendapatan;
        $saldoBulanlalu=$pendapatan->totalDanaMasuk($this->monthBefore($time))-$pengeluaran->totalDanaKeluar($this->monthBefore($time));
        $danakeluar = $pengeluaran->danakeluar($this->timeByMonth($time));
        $danamasuk = $saldoBulanlalu+$pendapatan->danamasuk($this->timeByMonth($time));
        $saldo=$danamasuk-$danakeluar;
        $gcPengeluaran = DB::table('group_category')->where('pengeluaran', '1')->get();
        $gcPendapatan = DB::table('group_category')->where('pendapatan', '1')->get();
        $textColor=['#4e73df','#f6c23e','#1cc88a','#e74a3b','#fd7e14','#36b9cc','#6f42c1','#858796'];

        // dd($groupCategory);
        $data=array(
            'danakeluar' => $this->rupiah($danakeluar),
            'danamasuk' => $this->rupiah($danamasuk),
            'saldo' => $this->rupiah($saldo),
            'monthYear' => $time,
            'gcPengeluaran' => $gcPengeluaran,
            'gcPendapatan' => $gcPendapatan,
            'textColor' => $textColor
        );
        return view('BudgetinQ.dashboard')->with($data);
    }
    // PER GROUP BY WAKTU
    public function chartArea(Request $request){
            $pengeluaran = new Pengeluaran;
            $rules = array(
                'time' => 'required|max:255'
            );
            $customMessages = [
                'time.required' => 'time error'
            ];
            $validator = $this->validate($request, $rules, $customMessages);
            $time = $this->dateFilter($validator['time']);
            $y = date("Y", strtotime($time));
            $m = date("m", strtotime($time));
            $d=cal_days_in_month(CAL_GREGORIAN,$m,$y);
            $labels = [];
            $totalPerHari = [];
            $getTotalPerHari = $pengeluaran->totalPerHariGroup($this->timeByMonth($time));
            foreach ($getTotalPerHari as $key => $value) {
                array_push($labels,$value->waktu);
                array_push($totalPerHari,$value->total);
            }
            if(empty($labels) && empty($totalPerHari)){
                $labels= [date("Y-m-d")];
                $totalPerHari = [0]; 
            }
            $data = array(
                'labels' => $labels,
                'totalPerHari' => $totalPerHari,
            );
            return $data;
    }

    // PER HARI 01-31
    // public function chartArea(Request $request){
    //     $pengeluaran = new Pengeluaran;
    //     $rules = array(
    //         'time' => 'required|max:255'
    //     );
    //     $customMessages = [
    //         'time.required' => 'time error'
    //     ];
    //     $validator = $this->validate($request, $rules, $customMessages);
    //     $time = $this->dateFilter($validator['time']);
    //     $y = date("Y", strtotime($time));
    //     $m = date("m", strtotime($time));
    //     $d=cal_days_in_month(CAL_GREGORIAN,$m,$y);
    //     $labels = [];
    //     $totalPerHari = [];
    //     for ($i=1; $i <= $d ; $i++) { 
    //         $i = str_pad($i, 2, '0', STR_PAD_LEFT);
    //         $date = "$y-$m-$i";
    //         array_push($labels,$date);
    //         array_push($totalPerHari,$pengeluaran->totalPerHari($date));
    //     }
    //     $data = array(
    //         'labels' => $labels,
    //         'totalPerHari' => $totalPerHari,
    //     );
    //     return $data;
    // }

    public function chartPie(Request $request){
        $pengeluaran = new Pengeluaran;
        $rules = array(
            'time' => 'required|max:255'
        );
        $customMessages = [
            'time.required' => 'time error'
        ];
        $validator = $this->validate($request, $rules, $customMessages);
        $time = $this->dateFilter($validator['time']);
        return $time;
    }
    
    public function danamasuk($time=null,$day=null){
        $time = $time ?: date("F, Y");
        $day = $day ?: date("d");
        $data=array(
            'monthYear' => $time
        );
        return view('BudgetinQ.danamasuk')->with($data);
    }

    public function danakeluar($time=null,$day=null){
        $title = "Daftar Pengeluaran $day $time";
        $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
        $day = $day ?: date("d");
        $data=array(
            'monthYear' => $time,
            'title' => $title
        );
        return view('BudgetinQ.danakeluar')->with($data);
    }

    public function vDKByK($id_jenis_pengeluaran=null,$time=null){
        $jenis_pengeluaran = DB::table('jenis_pengeluaran')->where('id_jenis_pengeluaran', $id_jenis_pengeluaran)->first();
        if($jenis_pengeluaran==null)
             return redirect()->to('/kategori/danakeluar');
        $title = "Daftar Pengeluaran";
        $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");

        // $day = $day ?: date("d");
        $data=array(
            'monthYear' => $time,
            'title' => $title,
            'jenis_pengeluaran' => DB::table('jenis_pengeluaran')->where('id', Auth::user()->id)->get(),
            'page' => 'kategori',
            'id_jenis_pengeluaran' => $id_jenis_pengeluaran
        );
        return view('BudgetinQ.danakeluar')->with($data);
    }

    public function danakeluarResponse($time=null,$day=null){
        $time = $time ?: date("F, Y");
        $pengeluaran = new Pengeluaran;
        $waktu = $this->timeByMonth($time);
        $list_pengeluaran = $pengeluaran->selectAll($waktu);
        if ($day) {
            $waktu = $this->timeByMonth($day.' '.$time); 
            $list_pengeluaran = $pengeluaran->selectRange($waktu['end_default'],$waktu['end_default']);
        }
        $data=array(
            'cPengeluaran' => DB::table('jenis_pengeluaran')->where('id', Auth::user()->id)->get(),
            'gcPengeluaran' => DB::table('group_category')->where('pengeluaran', '1')->get(),
            'list_pengeluaran' => $list_pengeluaran
        );
        return response()->json($data);
    } 

    public function danakeluarResponseByKategori($id_jenis_pengeluaran=null,$time=null){
        // dd($id_jenis_pengeluaran);
        $transaksi = new Transaksi;
        // $time = $time ?: date("F, Y");
        // dd($time);
        $pengeluaran = new Pengeluaran;
        if ($time == null) {
            $periode = $transaksi->sPeriode()[0];
            // dd($periode->awal);
            $list_pengeluaran = $pengeluaran->selectRangeByKategori($periode->awal,$periode->akhir,$id_jenis_pengeluaran);
        }else{
            $waktu = $this->timeByMonth($time);
        $list_pengeluaran = $pengeluaran->selectRangeByKategori($waktu['start_default'],$waktu['end_default'],$id_jenis_pengeluaran);
        }
        $data=array(
            'cPengeluaran' => DB::table('jenis_pengeluaran')->where('id', Auth::user()->id)->get(),
            'gcPengeluaran' => DB::table('group_category')->where('pengeluaran', '1')->get(),
            'list_pengeluaran' => $list_pengeluaran
        );
        return response()->json($data);
    }  

    

    public function dataGC(Request $request){
        $pengeluaran = new Pengeluaran;
        $pendapatan = new Pendapatan;
        $id=Auth::user()->id;
        $rules = array(
            'time' => 'required|max:255'
        );
        $customMessages = [
            'time.required' => 'time error'
        ];

        $validator = $this->validate($request, $rules, $customMessages);
        $time = $this->timeByMonth($validator['time']);
        
        $gcPengeluaran = DB::table('group_category')->where('pengeluaran', '1')->get();
        $gcPendapatan = DB::table('group_category')->where('pendapatan', '1')->get();
        $cPendapatan = DB::table('jenis_pendapatan')->where('id', $id)->get();
        $cPengeluaran = DB::table('jenis_pengeluaran')->where('id', $id)->get();
        $list_pemasukkan = $pendapatan->selectAll($time);
        $list_pengeluaran = $pengeluaran->selectAll($time);

        $data=array(
            'cPendapatan' => $cPendapatan,
            'cPengeluaran' => $cPengeluaran,
            'gcPengeluaran' => $gcPengeluaran,
            'gcPendapatan' => $gcPendapatan,
            'list_pengeluaran' => $list_pengeluaran,
            'list_pemasukkan' => $list_pemasukkan,
        );


        return $data;
    }

    public function categoryDK($gcID=null,$cID=null,$time=null){
        $transaksi = new Transaksi;
        $jenis_pengeluaran = new JenisPengeluaran;
        $time = $time ?: date("F, Y");
        if($gcID)
            if(count(DB::table('group_category')->where('group_category_id',$gcID)->get())==0)
                return redirect()->to('/kategori/danakeluar');
        if($cID)
            if(count(DB::table('jenis_pengeluaran')->where('id_jenis_pengeluaran',$cID)->get())==0)
                return redirect()->to('/kategori/danakeluar');
                // return response()->json(['errors' => ["Nama Kategori ini sudah ada"]], 422);
        $dateRange = $this->timeByMonth($time);
    
        // dd($cID,$gcID,$time);
        // dd($day);
        
        $periode = $transaksi->sPeriode()[0];
        if ($periode->awal == $periode->akhir) {
            $periode->periode = $periode->periode ?: 1;
            $periode->awal = $periode->awal ?: date("Y-m-d");
            $deskripsi='Periode : '.$periode->periode." Bulan ($periode->awal)";
        }else{
            $deskripsi='Periode : '.$periode->periode." Bulan ($periode->awal s/d $periode->akhir)";
        }
        $data=array(
            'monthYear' => $time,
            'deskripsi' => $deskripsi
        );
        // dd($data);
        return view('BudgetinQ.jenisPengeluaranCRUD.view')->with($data);
    }

    public function categoryDKResponse($gcID=null,$cID=null,$time=null){
        $transaksi = new Transaksi;
        $jenis_pengeluaran = new JenisPengeluaran;
        $time = $time ?: date("F, Y");
        if($gcID)
            if(count(DB::table('group_category')->where('group_category_id',$gcID)->get())==0)
                return response()->json(['errors' => ["Group Kategori tidak ada"]], 422);
        if($cID)
            if(count(DB::table('jenis_pengeluaran')->where('id_jenis_pengeluaran',$cID)->get())==0)
                return response()->json(['errors' => ["Jenis Pengeluaran tidak ada"]], 422);

        $data=array(
            'monthYear' => $time,
            'list_jenis_pengeluaran' => $jenis_pengeluaran->selectAll()
        );
        return response()->json($data);
    }

}



