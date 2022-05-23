<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\ArtistIdentity;
use App\Model\Device;
use App\Model\Transaction;
use App\Model\AuthPayment;
use App\Model\ArtistWallet;
use App\Model\KarmaPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use JWTAuth;

class AuthController extends Controller
{

    public function register(Request $request) {
        try {
            $input = $request->post();
            $rules = [
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6',
                'name'=>'',
                'fcm' => '',
                'type' => 'required',
            ];

            $this->validation($input,$rules);

            //get device id
            $getDevice = Device::where('id', $input['device_id'])->first();

            if($input['type'] == 1) {
                // INSERT VALUES
                $user = new User();
                $user->name = $input['name'];
                $user->email = $input['email'];
                $user->password = bcrypt($input['password']);
                $user->device_id =$getDevice->id;
                $user->fcm_token = $input['fcm'];
                $user->role_id = $input['type'];
                $user->is_active = 1;
                $user->profile_pic = 'no-image.png';
                $user->created_at = Carbon::now();
                $user->save();

                $token = JWTAuth::fromUser($user);

                 // update kPoints in user
                 $userPoint = KarmaPoint::where('user_id','=', $user->id)->first();
                 if(is_null($userPoint)) {
                     $ins = new KarmaPoint();
                     $ins->user_id = $user->id;
                     $ins->kPoints = (int)$getDevice->kPoints;
                     $ins->created_at = Carbon::now();
                     $ins->save();
                     $kp = (int)$getDevice->kPoints;
                 } else {
                     $userPoint->kPoints = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                     $userPoint->updated_at = Carbon::now();
                     $kp = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                     $userPoint->save();
                 }

                  // update transaction table
                  $tranCode = Transaction::where('device_id', '=', $input['device_id'])->first();

                  if(is_null($tranCode)) {
                      // Nothing to do
                  } else {
                      $tranCode->device_id = $user->id;
                      $tranCode->updated_at = Carbon::now();
                      $tranCode->save();
                  }

                $responseData = array(
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                    'token' => $token,
                    'id' => $user->id,
                    'kPoints' => $kp
                );

            } else if($input['type'] == 2) {
                    $userExists = User::where('id', '=', $input['artist_id'])->first();
                    $userExists->email = $input['email'];
                    $userExists->password = bcrypt($input['password']);
                    $userExists->device_id =$getDevice->id;
                    $userExists->fcm_token = $input['fcm'];
                    $userExists->role_id = $input['type'];
                    $userExists->is_active = 1;
                    $userExists->is_logged = 0;
                    $userExists->profile_pic = 'no-image.png';
                    $userExists->updated_at = Carbon::now();
                    $userExists->save();

                    $token = JWTAuth::fromUser($userExists);

                    $responseData = array(
                        'name' => $userExists->name,
                        'email' => $userExists->email,
                        'role_id' => $userExists->role_id,
                        'profile_pic' => url('/public/profile_images/'.$userExists->profile_pic),
                        'token' => $token,
                        'id' => $userExists->id,
                        'is_logged' => $userExists->is_logged,
                        'is_active' => $userExists->is_active,
                        'is_wallet' => 0,
                        'pendingReedemed' => 0
                    );
                // }

            }

            $this->success('Register Successfully',$responseData);

        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function codeCheck(Request $request) {
        try {
            $input = $request->post();
            $rules = [
                'name' => 'required',
                'artist_code' => 'required',
            ];

            $this->validation($input,$rules);

            $user = User::where('name','=', $input['name'])->first();

            // $this->error(isset($user->email));

            if(is_null($user)) {
                $this->error('Name not found. Please check again.');
            } else {
                if(!isset($user->email)) {
                    //check code
                    $checkCode = ArtistIdentity::where('unique_id', '=', $input['artist_code'])->first();

                    if(is_null($checkCode)) {
                        $this->error('Code not found. Please check again.');
                    } else {
                        $this->success('Code matched successfully.', $user->id);
                    }
                } else {
                    $this->error('Your email is already setup, please login.');
                }

            }

        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function login(Request $request) {
        try {
             $input = $request->post();
             $rules = [
                 'email' => 'required|email|max:255',
                 'password' => 'required|min:6',

             ];
             $this->validation($input,$rules);

             if (!$token = auth('api')->attempt([
                'email' => $input['email'],
                'password' => $input['password']
            ])) {
                $this->error('Incorrect email/password');
            } else {

                $user =  auth('api')->user();
                $user->fcm_token =  $input['fcm'];
                $user->device_id =  $input['device_id'];
                $user->save();

                // get Device

                if((int)$input['type'] == (int)$user->role_id && $user->is_active == 1)  {

                    if($user->role_id === 2 ) {
                        if($user->is_logged == 0) {
                            $user->is_logged = 1;
                            $user->save();
                        }

                        // check user Wallet
                        $checkWallet = ArtistWallet::where('artist_id','=', $user->id)->first();

                        if(!is_null($checkWallet)) {
                            //check tip

                            $points = Transaction::where('artist_id', $user->id)->where('is_success', 1)->where('is_redemed', 0)->sum('user_amount');

                            $responseData = array(
                                'name' => $user->name,
                                'email' => $user->email,
                                'role_id' => $user->role_id,
                                'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                                'token' => $token,
                                'is_logged' => $user->is_logged,
                                'is_active' => $user->is_active,
                                'id' => $user->id,
                                'is_wallet' => 1,
                                'pendingReedemed' => (int)$points
                            );
                        } else {
                            $responseData = array(
                                'name' => $user->name,
                                'email' => $user->email,
                                'role_id' => $user->role_id,
                                'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                                'token' => $token,
                                'is_logged' => $user->is_logged,
                                'is_active' => $user->is_active,
                                'id' => $user->id,
                                'is_wallet' => 0,
                                'pendingReedemed' => 0
                            );
                        }

                    } else {
                        $getDevice = Device::where('id', $input['device_id'])->first();
                        // update kPoints in user
                        $userPoint = KarmaPoint::where('user_id','=', $user->id)->first();
                        if(is_null($userPoint)) {
                            $ins = new KarmaPoint();
                            $ins->user_id =$user->id;
                            $ins->kPoints = (int)$getDevice->kPoints;
                            $ins->created_at = Carbon::now();
                            $ins->save();
                            $kp = (int)$getDevice->kPoints;
                        } else {
                            $userPoint->kPoints = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                            $userPoint->updated_at = Carbon::now();
                            $kp = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                            $userPoint->save();
                        }

                         // update transaction table
                      $tranCode = Transaction::where('device_id', '=', $input['device_id'])->first();

                      if(is_null($tranCode)) {
                          // Nothing to do
                      } else {
                          $tranCode->device_id = $user->id;
                          $tranCode->updated_at = Carbon::now();
                          $tranCode->save();
                      }

                        $responseData = array(
                            'name' => $user->name,
                            'email' => $user->email,
                            'role_id' => $user->role_id,
                            'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                            'token' => $token,
                            'id' => $user->id,
                            'kPoints' => $kp
                        );
                    }
                    $this->success('Login Successfully',$responseData);
                } else {
                    $this->error('credentials does not match our records');
                }
            }

        } catch(\Exception $e) {
            //  $this->error($e->getMessage());
            $this->error('Something went at the server end. Please check with our support team.');
        }

    }

    public function socialLogin(Request $request) {
        try {
             $input = $request->post();
             $rules = [
                 'name' => 'required'
             ];
             $term = strtolower($input['name']);

            if($input['type'] === 1) {
                $checkValue = User::whereRaw('LOWER(name) like (?)', ["%{$term}%"])->where('role_id', '=', 1)->first();
                 //get device id

                if(!is_null($checkValue) && $checkValue->is_active == 1) {
                    if($checkValue->is_active == 1) {
                        $getDevice = Device::where('id', $checkValue->device_id)->first();
                        $token = JWTAuth::fromUser($checkValue);

                        // update kPoints in user
                        $userPoint = KarmaPoint::where('user_id','=', $user->id)->first();
                        if(is_null($userPoint)) {
                            $ins = new KarmaPoint();
                            $ins->user_id = $checkValue->id;
                            $ins->kPoints = (int)$getDevice->kPoints;
                            $ins->created_at = Carbon::now();
                            $ins->save();
                            $kp = (int)$getDevice->kPoints;
                        } else {
                            $userPoint->kPoints = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                            $userPoint->updated_at = Carbon::now();
                            $kp = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                            $userPoint->save();
                        }

                        // update transaction table
                        $tranCode = Transaction::where('device_id', '=', $checkValue->device_id)->first();

                        if(is_null($tranCode)) {
                            // Nothing to do
                        } else {
                            $tranCode->device_id = $checkValue->id;
                            $tranCode->updated_at = Carbon::now();
                            $tranCode->save();
                        }

                        $responseData = array(
                            'name' => $checkValue->name,
                            'email' => $checkValue->email,
                            'role_id' => $checkValue->role_id,
                            'profile_pic' => url('/public/profile_images/'.$checkValue->profile_pic),
                            'token' => $token,
                            'id' => $checkValue->id,
                            'kPoints' => $kp
                        );
                    } else {
                        $this->error('Please connect with our support team as your account is currently not active.');
                    }

                } else {
                    //get device id
                    $getDevice = Device::where('id', $input['device_id'])->first();

                    // INSERT VALUES
                    $user = new User();
                    $user->name = $input['name'];
                    $user->email = $input['email'];
                    $user->password = bcrypt(123456);
                    $user->device_id =$getDevice->id;
                    $user->fcm_token = $input['fcm_token'];
                    $user->role_id = $input['type'];
                    $user->is_active = 1;
                    $user->profile_pic = 'no-image.png';
                    $user->created_at = Carbon::now();
                    $user->save();

                    $token = JWTAuth::fromUser($user);

                     // update kPoints in user
                     $userPoint = KarmaPoint::where('user_id','=', $user->id)->first();
                     if(is_null($userPoint)) {
                         $ins = new KarmaPoint();
                         $ins->user_id =(int)$getDevice->kPoints;
                         $ins->kPoints = $karmaPoints;
                         $ins->created_at = Carbon::now();
                         $ins->save();
                         $kp = (int)$getDevice->kPoints;
                     } else {
                         $userPoint->kPoints = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                         $userPoint->updated_at = Carbon::now();
                         $kp = (int)$userPoint->kPoints + (int)$getDevice->kPoints;
                         $userPoint->save();
                     }
                      // update transaction table
                      $tranCode = Transaction::where('device_id', '=', $input['device_id'])->first();

                      if(is_null($tranCode)) {
                          // Nothing to do
                      } else {
                          $tranCode->device_id = $checkValue->id;
                          $tranCode->updated_at = Carbon::now();
                          $tranCode->save();
                      }

                    $responseData = array(
                        'name' => $user->name,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                        'token' => $token,
                        'id' => $user->id,
                        'kPoints' => $kp
                    );
                }

             } else {

                $checkValue = User::whereRaw('LOWER(name) like (?)', ["%{$term}%"])->where('role_id', '=', 2)->first();

                if(!is_null($checkValue) ) {
                    if($checkValue->is_active == 1) {
                        //get device id
                        $getDevice = Device::where('id', $checkValue->device_id)->first();

                        $token = JWTAuth::fromUser($user);
                        $responseData = array(
                            'name' => $checkValue->name,
                            'email' => $checkValue->email,
                            'role_id' => $checkValue->role_id,
                            'profile_pic' => url('/public/profile_images/'.$checkValue->profile_pic),
                            'token' => $token,
                            'is_logged' => $checkValue->is_logged,
                            'id' => $checkValue->id,
                            'is_active' => $user->is_active
                        );
                    } else {
                        $this->error('Please connect with our support team as your account is currently not active.');
                    }


                } else {
                      //get device id
                      $getDevice = Device::where('id', $input['device_id'])->first();

                    // INSERT VALUES
                    $user = new User();
                    $user->name = $input['name'];
                    $user->email = $input['email'];
                    $user->password = bcrypt(123456);
                    $user->device_id =$getDevice->id;
                    $user->fcm_token = $input['fcm_token'];
                    $user->role_id = $input['type'];
                    $user->is_active = 0;
                    $user->is_logged = 0;
                    $user->profile_pic = 'no-image.png';
                    $user->created_at = Carbon::now();
                    $user->save();

                    $token = JWTAuth::fromUser($user);

                    $responseData = array(
                        'name' => $user->name,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                        'token' => $token,
                        'id' => $user->id,
                        'is_logged' => $user->is_logged,
                        'is_active' => $user->is_active
                    );
                }
             }
            //  $this->error($e->getMessage());
             $this->success('User logged in successfully', $responseData);

        } catch(\Exception $e) {
        $this->error($e->getMessage());
            $this->error('Something went at the server end. Please check with our support team.');
        }

    }

    public function addDevice(Request $request) {
        try {
            $input = $request->post();

            // check if it already exits
            $checkExist = Device::where('device_id', $input['device_id'])->first();

            if(!is_null($checkExist)) {
                $deviceID = $checkExist->id;
             } else {
                $device = new Device();
                $device->device_id = $input['device_id'];
                $device->platform = $input['platform'];
                $device->created_at = Carbon::now();
                $device->save();

                $deviceID = $device->id;
            }

            $this->success('Device added successfully',array(
                'id' => $deviceID
            ));

        } catch(\Exception $e) {
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function profileUpdate(Request $request) {
        try {
            $token = $request->bearerToken();
            $user = JWTAuth::toUser($token);

            $input = $request->post();


            if(isset($input['name']) && !empty($input['name']) && !is_null($input['name'])) {
                $user->name = $input['name'];
            } else {
                $this->error('Name is required');
            }

            if(!strpos($input['profile_pic'], "https://")) {
               $image = $this->imageUpload($input['profile_pic'], '/var/www/coversinplay/public/profile_images/');
               $user->profile_pic = $image;
           }

            if(isset($input['email']) && !empty($input['email']) && !is_null($input['email'])) {
                $user->email = $input['email'];
            } else {
                $this->error('Email is required');
            }


            $update = $user->save();

            $getDevice = Device::where('id', $user->device_id)->first();
            if($update) {

               $responseData = array(
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'profile_pic' => url('/public/profile_images/'.$user->profile_pic),
                    'token' => $token,
                    'id' => $user->id,
                    'kPoints' => $getDevice->kPoints
               );
               $this->success('Your profie has been updated successfully.', $responseData);
            } else {
                $this->error('Something went wrong');
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }

    public function changePassword(Request $request) {
        try {
            $input = $request->post();
            $token = $request->bearerToken();
            $user = JWTAuth::toUser($token);

            $this->validate($request, [
                'oldPassword' => 'required',
                'newPassword' => 'confirmed|max:6|different:oldPassword',
            ]);

            if (Hash::check($request->oldPassword, $user->password)) {
               $user->fill([
                'password' => Hash::make($request->new_password)
                ])->save();
                $this->success('Your password has been updated');
            } else {
                $this->error('Old PAssword does not match');
            }

       } catch(\Exception $e) {
           //  $this->error($e->getMessage());
           $this->error('Something went at the server end. Please check with our support team.');
       }
    }

    public function forgotPassword(Request $request){
        try {
            $input = $request->post();

            $rules = [
                'email' => 'email|required'
            ];
            $this->validation($input,$rules);

            $checkExist = User::where('email','=', $input['email'])->where('role_id', '=', $input['type'])->first();

            if(is_null($checkExist)) {
                $this->error("This email does not exists in our record. You can create your account by signing up",[] );
            } else {
                if($checkExist->is_active == 0) {
                    $this->error("We are verifying your account. We will notify you once verification is done.",[] );
                } else {
                    // send email
                    $checkExist->password = bcrypt('123456');
                    $checkExist->updated_at = carbon::now();
                    $checkExist->save();

                    $data = array(
                        'password' => $this->randomPass(6),
                        'name' => $checkExist->name
                    );
                    Mail::send('emails.forgot',$data, function($m)use($checkExist) {
                        $m->to($checkExist->email)
                            ->subject("Forgot Password");
                    });

                    if (Mail::failures()) {
                        $this->error('Something went wrong with the server', array());
                    }else{
                        $this->success('Email has been sent successfully',array());
                    }
                }

            }


       } catch(\Exception $e) {
            $this->error($e->getMessage());
        //    $this->error('Something went at the server end. Please check with our support team.');
       }
    }

    public function addWallet(Request $request) {
        try {
            $input = $request->post();

            $user = User::where('id', $input['user_id'])->first();
            $user->is_logged = 1;
            $user->save();

             // add Wallet
             $wallet = new ArtistWallet();
             $wallet->artist_id = $input['user_id'];
             $wallet->wallet_type = $input['wallet_type'];
             $wallet->walled_id = $input['walled_id'];
             $wallet->created_at = Carbon::now();
             $wallet->save();

             $this->success('Details added successfully', []);

        }  catch(\Exception $e) {
            //  $this->error($e->getMessage());
            $this->error('Something went at the server end. Please check with our support team.');
        }

    }

    public function refreshToken($refresh_token) {
        try {

            $code = $request->query('code');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://accounts.spotify.com/api/token");
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "grant_type=refresh_token&refresh_token=$refresh_token");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic MzFhMDQzMGJiOWZlNGUxNGFjZmRmOTU0N2FkM2FkMDQ6Y2Y5NzZlYjNhYzQzNDg5YWJlMTMxOTc4ODhkYTEyZGE='
            ));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $result = json_decode($server_output);

            dd($result);

        } catch(\Exception $e) {
            $this->error('Something went at the server end. Please check with our support team.');
        }

    }

    public function spotifyProfile($token) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/me");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $token",
                'Content-Type:application/json'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $result = json_decode($server_output);

            return $result;

        } catch(\Exception $e) {
            $this->error($e->getMessage());
            $this->error('Something went at the server end. Please check with our support team.');
        }
    }
}
