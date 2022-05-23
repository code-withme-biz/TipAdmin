<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use App\Models\Customer;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $input = $request->post();
            $rules = [
                'email' => 'required|email|max:255|unique:customer',
                'password' => 'required|min:6',
                'first_name'=>'required',
                'last_name'=>'required',
                'store_name'=>'required',
                'phone_number' =>'required',
                'street' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required'
            ];

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {

                $this->error($validator->messages()->all()[0],null);
            }else{
                $user = new Customer();
                $user->first_name = $input['first_name'];
                $user->last_name = $input['last_name'];
                $user->email = $input['email'];
                $user->store_name = $input['store_name'];
                $user->phone_number = $input['phone_number'];
                $user->street = $input['street'];
                $user->city = $input['city'];
                $user->state = $input['state'];
                $user->zip = $input['zip'];
                $user->password = bcrypt($input['password']);
                $user->profile_pic = 'no-image.png';
                $user->created_at = Carbon::now();
                $user->save();

                $token = JWTAuth::fromUser($user);

                $responseData = array(
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'token' => $token,
                    'id' => $user->cust_id,
                    'store_name' => $user->store_name,
                    'phone_number' => $user->phone_number,
                    'street' => $user->street,
                    'city' => $user->city,
                    'state' => $user->state,
                    'zip' => $user->zip,
                    'profile_pic' => $user->profile_pic
                );

                $this->success('Register Successfully',$responseData);
            }

        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function login(Request $request){
        try {
            $input = $request->post();

            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required|min:6',
            ];

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                $this->error($validator->messages()->all()[0],null);
            }else{

                if (!$token = auth('api')->attempt([
                    'email' => $input['email'],
                    'password' => $input['password']
                ])) {
                    $this->error('Incorrect email/password');
                } else {

                    // $user =  auth('api')->user();
                    // $user->fcm_token = isset($input['fcm'])?$input['fcm']:'1234567890' ;
                    // $user->save();

                    $responseData = array(
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'token' => $token,
                        'id' => $user->cust_id,
                        'store_name' => $user->store_name,
                        'phone_number' => $user->phone_number,
                        'street' => $user->street,
                        'city' => $user->city,
                        'state' => $user->state,
                        'zip' => $user->zip,
                        'profile_pic' => $user->profile_pic
                    );

                    $this->success('loggedin Successfully',$responseData);
                }
            }
        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function forgotPassword(Request $request) {
        try {
            $input = $request->post();
            $rules = [
                'email' => 'required|email|max:255'
            ];



            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                $this->error($validator->messages()->all()[0],null);
            }else{

                $checkEmail = BreederProfile::where('email', $input['email'])->first();

                if (is_null($checkEmail)) {
                    $this->error('Email not found in our record(s).');
                } else {
                        $checkEmail->password =  bcrypt('123456');
                        $checkEmail->save();

                        // send email
                        $data = array(
                            'name' => $checkEmail->user_mname,
                            'password' => '123456'
                        );


                        Mail::send('emails.forgotPassword',$data, function($m)use($data, $input) {
                            $m->to($input['email'])
                            ->subject("Puppy Tail - Forgot Password");
                        });

                        if (Mail::failures()) {
                            $this->error('Server error! Send in blue not activated');
                        } else {
                            $this->success('We have sent you an email regarding the same.', null);
                        }
                }
            }

        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function socialLogin(Request $request) {
        try {
            $input = $request->post();
            $rules = [
                'email' => 'required|email|max:255',
                'user_name'=>'required'
            ];

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {

                $this->error($validator->messages()->all()[0],null);
            }else{
                $checkData = BreederProfile::where('user_name','=',$input['user_name'])->first();
                if(is_null($checkData)) {
                    // insert data and signup
                    $user = new BreederProfile();
                    $user->user_name = $input['user_name'];
                    $user->email = $input['email'];
                    $user->password = bcrypt($input['user_name']);
                    $user->fcm_token = isset($input['fcm'])?$input['fcm']:'1234567890' ;
                    $user->is_active = 1;

                    // save facebook image
                    $image = 'user-'.uniqid().'.jpg';
                    /*Use file_put_contents to get and save image*/
                    file_put_contents(public_path().'/profile_images/'.$image, file_get_contents($input['image']));

                    $user->profile_pic = $image;
                    $user->created_at = Carbon::now();
                    $user->save();

                    $token = JWTAuth::fromUser($user);

                    $responseData = array(
                        'user_name' => $user->user_name,
                        'email' => $user->email,
                        'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                        'token' => $token,
                        'id' => $user->breeder_profile_id
                    );

                } else {
                    $checkData->user_name = $input['user_name'];
                    $checkData->email = $input['email'];
                    $checkData->password = bcrypt($input['user_name']);
                    $checkData->fcm_token = isset($input['fcm'])?$input['fcm']:'1234567890' ;
                    $checkData->is_active = 1;

                    // save facebook image
                    $image = 'user-'.uniqid().'.jpg';
                    /*Use file_put_contents to get and save image*/
                    file_put_contents(public_path().'/profile_images/'.$image, file_get_contents($input['image']));

                    $checkData->profile_pic = $image;
                    $checkData->updated_at = Carbon::now();
                    $checkData->save();

                    $token = JWTAuth::fromUser($checkData);

                    $responseData = array(
                        'user_name' => $checkData->user_name,
                        'email' => $checkData->email,
                        'profile_pic' => url('/public/profile_images/'.$checkData->profile_pic),
                        'token' => $token,
                        'id' => $checkData->breeder_profile_id
                    );
                }

                $this->success('user logged in', $responseData);
            }

        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }
}
