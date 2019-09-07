<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Pendapatan;
use App\Pengeluaran;
use App\Kategori;
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

}



