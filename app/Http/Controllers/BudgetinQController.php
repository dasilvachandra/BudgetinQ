<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Pendapatan;
use App\Pengeluaran;
use App\Transaksi;
use App\JenisPengeluaran;
use App\JenisPendapatan;

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
        $transaksi = new Transaksi;
        $pendapatan = new Pendapatan;
        $periode = $transaksi->sPeriode()[0];
        $totalDanamasuk = $pendapatan->totalDanaMasuk($periode->awal,$this->monthBefore($time));
        $totalDanaKeluar = $pengeluaran->totalDanaKeluar($periode->awal,$this->monthBefore($time));
        $saldoBulanLalu=$totalDanamasuk-$totalDanaKeluar;
        $dateRange = $this->timeByMonth($time);
        $danakeluar = $pengeluaran->totalDanakeluar($dateRange['start_default'],$dateRange['end_default']);
        $danamasuk = $saldoBulanLalu+$pendapatan->totalDanaMasuk($dateRange['start_default'],$dateRange['end_default']);
        $saldo=$danamasuk-$danakeluar;
        $gcPengeluaran = DB::table('group_category')->where('pengeluaran', '1')->get();
        $gcPendapatan = DB::table('group_category')->where('pendapatan', '1')->get();
        $textColor=['#4e73df','#f6c23e','#1cc88a','#e74a3b','#fd7e14','#36b9cc','#6f42c1','#858796'];
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

    // DANAMASUK
    
    public function danamasuk($time=null,$day=null){
        $pendapatan = new Pendapatan;
        $pengeluaran = new Pengeluaran;
        $transaksi = new Transaksi;
        $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
        if($day==null){
            $title = "$time";
        }else{
            $day = $day ?: date("d");
            $title = "$day $time";
        }
        $periode = $transaksi->sPeriode()[0];
        $totalDanaMasuk = $pendapatan->totalDanaMasuk($periode->awal,$this->monthBefore($time));
        $totalDanaKeluar = $pengeluaran->totalDanaKeluar($periode->awal,$this->monthBefore($time));
        $saldoBulanLalu = $totalDanaMasuk-$totalDanaKeluar;
        $dateRange = $this->timeByMonth($time);
        $totalDanaMasukBulanIni = $pendapatan->totalDanamasuk($dateRange['start_default'],$dateRange['end_default']);
        
        $data=array(
            'monthYear' => $time,
            'title' => $title,
            'danamasuk' => $this->rupiah($totalDanaMasukBulanIni),
            'saldoBulanLalu' => $this->rupiah($saldoBulanLalu),
            'totalDanaMasuk' => $this->rupiah($totalDanaMasukBulanIni+$saldoBulanLalu),
            'monthBefore' => date("F, Y", strtotime($this->monthBefore($time)))
        );
        return view('BudgetinQ.danamasuk')->with($data);
    }
    public function vDMByK($id_jenis_pendapatan=null,$time=null){
        $pendapatan = new Pendapatan;
        $pengeluaran = new Pengeluaran;
        $transaksi = new Transaksi;
        $jenis_pendapatan = DB::table('jenis_pendapatan')->where('id_jenis_pendapatan', $id_jenis_pendapatan)->first();
        if($jenis_pendapatan==null){
             return redirect()->to('/danamasuk/danakeluar');
        }

        if($time==null){
            $transaksi = new Transaksi;
            $periode = $transaksi->sPeriode()[0];
            $title = "Periode : ".$periode->awal." s/d ".$periode->akhir;
            $totalDanaMasuk = $pendapatan->totalDanaMasukByKategori($periode->awal,$periode->akhir,$id_jenis_pendapatan);
        }else{
            $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
            $title = "BULAN $time <a class='btn btn-info' href='/danamasuk/kategori/$id_jenis_pendapatan/' >Lihat semua Periode</a>";
            $dateRange = $this->timeByMonth($time);
            $totalDanaMasuk = $pendapatan->totalDanaMasukByKategori($dateRange['start_default'],$dateRange['end_default'],$id_jenis_pendapatan);
        }
        $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
        $dateRange = $this->timeByMonth($time);
        $totalDanaMasukBulanIni = $pendapatan->totalDanamasuk($dateRange['start_default'],$dateRange['end_default']);
        

        $periode = $transaksi->sPeriode()[0];
        $totalDanaMasuk = $pendapatan->totalDanaMasuk($periode->awal,$this->monthBefore($time));
        $totalDanaKeluar = $pengeluaran->totalDanaKeluar($periode->awal,$this->monthBefore($time));
        $saldoBulanLalu = $totalDanaMasuk-$totalDanaKeluar;
        
        $data=array(
            'danamasuk' => $this->rupiah($totalDanaMasukBulanIni),
            'saldoBulanLalu' => $this->rupiah($saldoBulanLalu),
            'totalDanaMasuk' => $this->rupiah($totalDanaMasukBulanIni+$saldoBulanLalu),
            'monthBefore' => date("F, Y", strtotime($this->monthBefore($time))),
            'monthYear' => $time,
            'title' => $title,
            'jenis_pendapatan' => DB::table('jenis_pendapatan')->where('id', Auth::user()->id)->get(),
            'page' => 'kategori',
            'id_jenis_pendapatan' => $id_jenis_pendapatan,
            'totalDanaKeluar' => $this->rupiah($totalDanaMasuk)
        );
        return view('BudgetinQ.danamasuk')->with($data);
    }

    public function danamasukResponse($time=null,$day=null){
        $time = $time ?: date("F, Y");
        $pendapatan = new Pendapatan;
        $waktu = $this->timeByMonth($time);
        $list_pendapatan = $pendapatan->selectAll($waktu);
        if ($day) {
            $waktu = $this->timeByMonth($day.' '.$time); 
            $list_pendapatan = $pendapatan->selectRange($waktu['end_default'],$waktu['end_default']);
        }
        $data=array(
            'cPendapatan' => DB::table('jenis_pendapatan')->where('id', Auth::user()->id)->get(),
            'gcPendapatan' => $pendapatan->GCPendapatan(),
            'list_pendapatan' => $list_pendapatan
        );
        return response()->json($data);
    } 
    public function danamasukResponseByKategori($id_jenis_pendapatan=null,$time=null){
        // dd($id_jenis_pendapatan);
        $transaksi = new Transaksi;
        // $time = $time ?: date("F, Y");
        // dd($time);
        $pendapatan = new Pendapatan;
        if ($time == null) {
            $periode = $transaksi->sPeriode()[0];
            // dd($periode->awal);
            $list_pendapatan = $pendapatan->selectRangeByKategori($periode->awal,$periode->akhir,$id_jenis_pendapatan);
        }else{
            $waktu = $this->timeByMonth($time);
        $list_pendapatan = $pendapatan->selectRangeByKategori($waktu['start_default'],$waktu['end_default'],$id_jenis_pendapatan);
        }
        $data=array(
            'cPendapatan' => DB::table('jenis_pendapatan')->where('id', Auth::user()->id)->get(),
            'gcPendapatan' => DB::table('group_category')->where('pendapatan', '1')->get(),
            'list_pendapatan' => $list_pendapatan
        );
        return response()->json($data);
    }  


    // DANAKELUAR

    public function danakeluar($time=null,$day=null){
        $pengeluaran = new Pengeluaran;
        $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
        if($day==null)
            $title = "$time";
        else
            $day = $day ?: date("d");
            $title = "$day $time";
        $dateRange = $this->timeByMonth($time);
        $totalDanaKeluar = $this->rupiah($pengeluaran->totalDanaKeluar($dateRange['start_default'],$dateRange['end_default']));
        $data=array(
            'monthYear' => $time,
            'title' => $title,
            'totalDanaKeluar' => $totalDanaKeluar
        );
        return view('BudgetinQ.danakeluar')->with($data);
    }

    public function vDKByK($id_jenis_pengeluaran=null,$time=null){
        $pengeluaran = new Pengeluaran;
        $jenis_pengeluaran = DB::table('jenis_pengeluaran')->where('id_jenis_pengeluaran', $id_jenis_pengeluaran)->first();
        if($jenis_pengeluaran==null){
             return redirect()->to('/kategori/danakeluar');
        }
        if($time==null){
            $transaksi = new Transaksi;
            $periode = $transaksi->sPeriode()[0];
            $title = "Periode : ".$periode->awal." s/d ".$periode->akhir;
            $totalDanaKeluar = $pengeluaran->totalDanaKeluarByKategori($periode->awal,$periode->akhir,$id_jenis_pengeluaran);
        }else{
            $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
            $title = "BULAN $time <a class='btn btn-info' href='/danakeluar/kategori/$id_jenis_pengeluaran/' >Lihat semua Periode</a>";
            $dateRange = $this->timeByMonth($time);
            $totalDanaKeluar = $pengeluaran->totalDanaKeluarByKategori($dateRange['start_default'],$dateRange['end_default'],$id_jenis_pengeluaran);
        }
        $time = date("F, Y", strtotime($this->dateFilter($time))) ? : date("F, Y");
        
        $data=array(
            'monthYear' => $time,
            'title' => $title,
            'jenis_pengeluaran' => DB::table('jenis_pengeluaran')->where('id', Auth::user()->id)->get(),
            'page' => 'kategori',
            'id_jenis_pengeluaran' => $id_jenis_pengeluaran,
            'totalDanaKeluar' => $this->rupiah($totalDanaKeluar)
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
            'gcPengeluaran' => $pengeluaran->GCPengeluaran(),
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

    public function kategoriDK($a=null){
        $transaksi = new Transaksi;
        $jenis_pengeluaran = new JenisPengeluaran;
        $time = $a ?: date("F, Y");
        $time = date("F, Y", strtotime($this->timeByMonth($time)['end_default']));
        $periode = $transaksi->sPeriode()[0];
        $title='Periode : '.$periode->periode." Bulan ($periode->awal s/d $periode->akhir)";
        if ($periode->awal == $periode->akhir) {
            // untuk pengguna baru
            $periode->periode = $periode->periode ?: 1;
            $periode->awal = $periode->awal ?: date("Y-m-d");
            $title='Periode : '.$periode->periode." Bulan ($periode->awal)";
        }
        if ($a!=null) {
            $title="Periode :  Bulan $time";
        }
        $data=array(
            'monthYear' => $time,
            'title' => $title
        );
        // dd($data);
        return view('BudgetinQ.jenisPengeluaranCRUD.view')->with($data);
    }

    public function kategoriDKResponse($time=null){
        $transaksi = new Transaksi;
        $jenis_pengeluaran = new JenisPengeluaran;
        if ($time!=null) {
            $dateRange = $this->timeByMonth($time);
            $list_pengeluaran= $jenis_pengeluaran->selectRange($dateRange['start_default'],$dateRange['end_default']);
        }else{
            // $time = $time ?: date("F, Y");
            $periode = $transaksi->sPeriode()[0];
            // $title = "Periode : ".$periode->awal." s/d ".$periode->akhir;
            // $dateRange = $this->timeByMonth($time);
            $list_pengeluaran=$jenis_pengeluaran->selectRange($periode->awal,$periode->akhir);
        }

        $data=array(
            'monthYear' => $time,
            'list_jenis_pengeluaran' => $list_pengeluaran,
            'gcPengeluaran' => $jenis_pengeluaran->GCPengeluaran()
        );
        return response()->json($data);
    }

    public function categoryDM($a=null){
        $transaksi = new Transaksi;
        $jenis_pendapatan = new JenisPendapatan;
        $time = $a ?: date("F, Y");
        $time = date("F, Y", strtotime($this->timeByMonth($time)['end_default']));
        $periode = $transaksi->sPeriode()[0];
        $title='Periode : '.$periode->periode." Bulan ($periode->awal s/d $periode->akhir)";
        if ($periode->awal == $periode->akhir) {
            // untuk pengguna baru
            $periode->periode = $periode->periode ?: 1;
            $periode->awal = $periode->awal ?: date("Y-m-d");
            $title='Periode : '.$periode->periode." Bulan ($periode->awal)";
        }
        if ($a!=null) {
            $title="Periode :  Bulan $time";
        }
        $data=array(
            'monthYear' => $time,
            'title' => $title
        );
        // dd($data);
        return view('BudgetinQ.jenisPendapatanCRUD.view')->with($data);
    }

    public function categoryDMResponse($time=null){
        $transaksi = new Transaksi;
        $jenis_pendapatan = new JenisPendapatan;
        if ($time!=null) {
            $dateRange = $this->timeByMonth($time);
            $list_pendapatan= $jenis_pendapatan->selectRange($dateRange['start_default'],$dateRange['end_default']);
        }else{
            // $time = $time ?: date("F, Y");
            $periode = $transaksi->sPeriode()[0];
            // $title = "Periode : ".$periode->awal." s/d ".$periode->akhir;
            // $dateRange = $this->timeByMonth($time);
            $list_pendapatan=$jenis_pendapatan->selectRange($periode->awal,$periode->akhir);
        }

        $data=array(
            'monthYear' => $time,
            'list_jenis_pendapatan' => $list_pendapatan,
            'gcPendapatan' => $jenis_pendapatan->GCPendapatan(),
        );
        return response()->json($data);
    }

}



