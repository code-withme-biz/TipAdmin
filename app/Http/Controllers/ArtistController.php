<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class ArtistController extends Controller
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
        $userList = DB::table('users')
        ->leftJoin('artist_identity as s','s.artist_id', 'users.id')
        ->leftJoin('artist_songs as rs', 'rs.artist_id', 'users.id')
        ->where('role_id', '=', 2)
        ->orderBy('users.id', 'DESC')
        ->get(['users.*', 's.unique_id', 'rs.song']);

        $resultArray = [];

        foreach($userList as $ul) {
            $tipCount = Transaction::where('artist_id','=', $ul->id)->sum('user_amount');
            $rdCount = Transaction::where('artist_id','=', $ul->id)->where('is_redemed','=',1)->sum('user_amount');
            $pdCount = Transaction::where('artist_id','=', $ul->id)->where('is_redemed','=',0)->sum('user_amount');

            $resultArray[] = array(
                "id" => $ul->id,
                "device_id" => $ul->device_id,
                "name" => $ul->name,
                "email" => $ul->email,
                "is_active" => $ul->is_active,
                "is_logged" => $ul->is_logged,
                "profile_pic" => $ul->profile_pic,
                "created_at" => $ul->created_at,
                "updated_at" => $ul->updated_at,
                "unique_id" => $ul->unique_id,
                "song" => $ul->song,
                "tip_count" => round($tipCount, 2),
                "red_count" => round($rdCount, 2),
                "pend_count" => round($pdCount,2)
            );
        }

        return view('artist.index',compact(
            'resultArray'
        ));
    }

    public function add()
    {
        return view('artist.create');
    }

    public function insert(Request $request)
    {
        $requestData = $request->post();

        $rules = array(
            'name' => 'required|min:3|max:50',
            'email' => 'email',
            'password' => 'required|confirmed|min:6',
        );

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {

           $insertData = [
                'name' => $requestData['name'],
                'email' => $requestData['email'],
                'password' => bcrypt($requestData['password']),
                'profile_pic' => 'no-image.png',
                'role_id' => 2,
                'is_active' => 1,
                'fcm_token' => 'www'
            ];

            DB::table('users')->insert($insertData);

            return redirect()->to('/artist')->with('success', 'User inserted successfully!');
        }
    }


    public function edit($user_id)
    {
        $userData = User::find($user_id);
        return view('artist.edit',compact(
            'userData'
        ));
    }

    public function update(Request $request)
    {
        $requestData = $request->post();

        $rules = array(
            'name' => 'required|min:3|max:50',
            'email' => 'email',
            'password' => 'confirmed',
        );

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {

            $user =User::where('id',$requestData['user_id'])->first();
            $user->name = $requestData['name'] == '' || $requestData['name'] == null ? '':$requestData['name'];
            $user->email = $requestData['email'] == '' || $requestData['email'] == null ? '':$requestData['email'];
            if($requestData['password'] != null || $requestData['password'] != '') {
                $user->password = bcrypt($requestData['password']);
            }

            $user->save();

            return redirect()->to('/artist')->with('success', 'User updated successfully!');
        }


    }

    public function changeStatus($user_id, $active) {
        $user = User::where('id',$user_id)->where('role_id', 2)->first();
        $user->is_active = $active;
        $user->updated_at = Carbon::now();
        $user->save();

        return redirect()->to('/artist')->with('success', 'Status changed successfully!');
    }

    public function delete($user_id) {
        $user = User::find($user_id)->delete();
        return redirect()->to('/artist')->with('success', 'User deleted successfully!');
    }
}
