<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Pendapatan;
use App\Pengeluaran;

class BudgetinQController  extends Controller
{
    public $date;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        // echo date("F, Y");
        $time = "July, 2019";
        $pengeluaran = new Pengeluaran;
        $pendapatan = new Pendapatan;

        $danamasuk = $pendapatan->danamasuk($this->timeByMonth($time));
        $danakeluar = $pengeluaran->danakeluar($this->timeByMonth($time));
        $saldo=$danamasuk-$danakeluar;

        // dd($danakeluar);
        $data=array(
            'danakeluar' => $this->rupiah($danakeluar),
            'danamasuk' => $this->rupiah($danamasuk),
            'saldo' => $this->rupiah($saldo)
        );
        return view('BudgetinQ.dashboard')->with($data);
    }
}
