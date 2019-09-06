<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class BudgetinQController  extends Controller
{
    public $date;

    public function __construct()
    {
        
        // $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // dd($actual_link);

        $this->middleware('auth');
    }

    public function dashboard()
    {
        $data='';
        return view('BudgetinQ.dashboard')->with($data);
    }
}
