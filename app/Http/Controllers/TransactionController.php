<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class TransactionController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        // DB::enableQueryLog();
        $userList = DB::table('transaction')
        ->leftJoin('users as u','transaction.artist_id', 'u.id')
        ->leftJoin('users as un','transaction.device_id', 'un.id')
        ->where('u.role_id', '=', 2)
        ->orderBy('transaction.created_at', 'DESC')
        ->get(['u.name','transaction.*','un.name as user_name']);

        $resultArray = [];

        foreach($userList as $ul) {

            $resultArray[] = array(
                "name" => $ul->name,
                "user_name" => $ul->user_name? $ul->user_name: 'Guest',
                'trn_id' => $ul->trxn_id,
                'is_redemeeded' => $ul->is_redemed,
                'created_at' => date('d M Y',  strtotime($ul->created_at)),
                "tip_count" => round($ul->total_amount, 2),
                "art_count" => round($ul->user_amount, 2),
                "admin_count" => round($ul->admin_amount,2)
            );
        }

        return view('transaction.index',compact(
            'resultArray'
        ));
    }

}
