<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Device;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
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
        $userList = User::where('role_id', '=', 1)
        ->leftJoin('user_kps', 'user_kps.user_id', '=', 'users.id')
        ->get(['users.*','user_kps.kPoints']);

        return view('users.index',compact(
            'userList'
        ));
    }

    public function unregisteredUser() {
        DB::enableQueryLog();
        $deviceList = Device::leftJOIN('users', 'users.device_id', '!=','device.id')
         ->where('users.device_id', '=', Null)
        ->where('users.role_id', '=', 1)->get(['users.*']);
        dd(DB::getQueryLog());
        dd($deviceList);
        // $userList = User::where('role_id', '=', 1)->get();
        return view('users.index',compact(
            'userList'
        ));
    }

    public function add()
    {
        return view('users.create');
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
                'device_id' => 0,
                'password' => bcrypt($requestData['password']),
                'profile_pic' => 'no-image.png',
                'role_id' => 1,
                'is_active' => 1,
                'fcm_token' => 'www'
            ];

            DB::table('users')->insert($insertData);

            return redirect()->to('/user')->with('success', 'User inserted successfully!');
        }

    }

    public function edit($user_id)
    {
        $userData = User::find($user_id);
        return view('users.edit',compact(
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
            $user->role_id = 1;
            $user->email = $requestData['email'] == '' || $requestData['email'] == null ? '':$requestData['email'];
            if($requestData['password'] != null || $requestData['password'] != '') {
                $user->password = bcrypt($requestData['password']);
            }

            $user->save();

            //send welcome mail
            // Mail::to($requestData['email'])->send(
            //     new WelcomeMail(
            //         array(
            //             'username' => $requestData['user_name'],
            //             'password' => $requestData['password']
            //         )
            //     )
            // );
            return redirect()->to('/user')->with('success', 'User updated successfully!');
        }


    }

    public function changeStatus($user_id, $active) {
        $user = User::where('id',$user_id)->where('role_id', 1)->first();
        $user->is_active = $active;
        $user->updated_at = Carbon::now();
        $user->save();

        return redirect()->to('/user')->with('success', 'Status changed successfully!');
    }

    public function delete($user_id) {
        $user = User::find($user_id)->delete();
        return redirect()->to('/user')->with('success', 'User deleted successfully!');
    }
}
