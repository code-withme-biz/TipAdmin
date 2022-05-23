<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Model\User;
use App\Model\Transaction;
use App\Model\Device;
use App\Model\AuthPayment;
use App\Model\ArtistWallet;
use App\Model\ArtistSong;
use App\Model\KarmaPoint;
use App\Model\Message;
use Carbon\Carbon;
use JWTAuth;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

    public function paymentForm(Request $request) {
        try {

            $amount = (float)$request->query('amount');
            $device_id = $request->query('device_id');
            $artist_name = ucwords($request->query('artist_name'));
            $album_name = ucwords($request->query('album'));

            // check artist exits
            $term = strtolower($artist_name);
            $artistCheck = User::whereRaw('lower(name) like (?)',["%{$term}%"])->where('role_id','=', 2)->first();

            if(is_null($artistCheck)) {
                $insertData = [
                    'name' => $artist_name,
                    'password' => bcrypt('123456'),
                    'profile_pic' => 'no-image.png',
                    'device_id' => 0,
                    'role_id' => 2,
                    'is_active' => 0,
                    'is_logged' => 0,
                    'fcm_token' => 'www'
                ];

                $artist_id = DB::table('users')->insertGetId($insertData);

                // Add artist Identity
                $insertSpotify = [
                    'unique_id' => $this->random_strings(4),
                    'artist_id' => $artist_id
                ];
                $insertSpotData = DB::table('artist_identity')->insertGetId($insertSpotify);

                $intent = 'authorize';

                // Add Song
                $artistSong = new ArtistSong();
                $artistSong->artist_id = $artist_id;
                $artistSong->song = $album_name;
                $artistSong->created_at = carbon::now();
                $artistSong->save();

            } else {
                $checkWallet = ArtistWallet::where('artist_id', '=', $artistCheck->id)->first();
                if(is_null($checkWallet)) {
                    $artist_id = $artistCheck->id;
                    $intent = 'authorize';
                } else {
                    $artist_id = $artistCheck->id;
                    $intent = 'sale';
                }

            }


            return view('welcome', compact(
                'amount',
                'device_id',
                'artist_id',
                'intent'
            ));


        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function createPayment(Request $request) {
        try {
            $input = $request->post();

            $token = $this->generateToken();

            $karmaPoints  = (float)$input['amount'] * 20;

            $deviceUpdate = Device::where('id', '=', $input['device_id'])->first();
            $deviceUpdate->kPoints = $karmaPoints;
            $deviceUpdate->updated_at = Carbon::now();
            $deviceUpdate->save();
            @$trn_id;

            if($input['tranType'] === 'sale') {
                // create tranaction
               $transaction = new Transaction();
               $transaction->device_id = $input['device_id'];
               $transaction->total_amount = $input['amount'];
               $transaction->user_amount = ($input['amount']/100)*85;
               $transaction->admin_amount = ($input['amount']/100)*15;
               $transaction->artist_id = $input['artist_id'];
               $transaction->created_at = Carbon::now();
               $transaction->is_success = 0;
               $transaction->is_redemed = 0;
               $transaction->save();
               $trn_id = $transaction->id;
               $type = 'sale';

           } else if($input['tranType'] === 'authorize') {
               $transaction = new AuthPayment();
               $transaction->device_id = $input['device_id'];
               $transaction->amount = $input['amount'];
               $transaction->artist_id = $input['artist_id'];
               $transaction->created_at = Carbon::now();
               $transaction->save();

               $trn_id = $transaction->id;

               $type = 'authorize';
           }



            $postRequest = array(
                "intent" => $input['tranType'],
                "payer" => array (
                    "payment_method" =>  "paypal"
                ),
                "transactions" => array(
                    array(
                        "amount" => array(
                            "total" =>  $input['amount'],
                            "currency" => "USD"
                        )
                    )
                ),
                "redirect_urls" => array(
                    "return_url" => "https://www.dapperitmedia.com/api/payment/success?type=".$type."&artist_id=".$input['artist_id'].'&kpoints='.$karmaPoints.'&trn_id='.$trn_id,
                    // "return_url" => "https://tip-app-bd988.web.app/thank-you?type=".$type."&artist_id=".$input['artist_id']."&kpoints=".$karmaPoints."&trn_id=".$trn_id,
                    "cancel_url" => "https://www.dapperitmedia.com/api/payment/failed"
                )
            );


            $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payment');
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $token",
                        'Content-Type:application/json'
            ));
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);

            $apiResponse = json_decode($apiResponse);

            return $apiResponse->links[1]->href;

        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
            $this->error('Something went wrong at the server end.',[]);
        }
    }

    public function failiurePage() {
        return view('faliure');
    }

    public function successPage(Request $request) {
        try {
            $input = $request->query();
            $token = $this->generateToken();

            if($input['type'] === 'authorize') {

                  // Execute Payment
                    $executePost = array(
                        'payer_id' => $input['PayerID']
                    );

                    $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payment/'.$input['paymentId'].'/execute');
                    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $token",
                            'Content-Type:application/json'
                    ));
                    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($executePost));
                    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec($cURLConnection);
                    curl_close($cURLConnection);

                    $response = json_decode($response);

                    //Authorise Payment

                    $auth_id = $response->transactions[0]->related_resources[0]->authorization->id;

                    $auth_link = $response->transactions[0]->related_resources[0]->authorization->links[0]->href;

                    $saleConn = curl_init($auth_link);
                    curl_setopt($saleConn, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $token",
                            'Content-Type:application/json'
                    ));
                    curl_setopt($saleConn, CURLOPT_RETURNTRANSFER, true);

                    $sale = curl_exec($saleConn);
                    curl_close($saleConn);

                    $sale = json_decode($sale);


                    $authUpdate = AuthPayment::where('id', '=', $input['trn_id'])->first();
                    $authUpdate->auth_id = $auth_id;
                    $authUpdate->payer_id = $input['PayerID'];
                    $authUpdate->updated_at = Carbon::now();
                    $authUpdate->save();

                    return view('success');

            } else if($input['type'] === 'sale') {
                 // Execute Payment
                 $executePost = array(
                    'payer_id' => $input['PayerID']
                );

                $execConnection = curl_init('https://api.paypal.com/v1/payments/payment/'.$input['paymentId'].'/execute');
                curl_setopt($execConnection, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $token",
                        'Content-Type:application/json'
                ));
                curl_setopt($execConnection, CURLOPT_POSTFIELDS, json_encode($executePost));
                curl_setopt($execConnection, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($execConnection);
                curl_close($execConnection);

                $response = json_decode($response);

                $sale_id = $response->transactions[0]->related_resources[0]->sale->id;
                $sale_link = $response->transactions[0]->related_resources[0]->sale->links[0]->href;

                // Capture Payment
                $saleConn = curl_init($sale_link);
                curl_setopt($saleConn, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $token",
                        'Content-Type:application/json'
                ));
                curl_setopt($saleConn, CURLOPT_RETURNTRANSFER, true);

                $sale = curl_exec($saleConn);
                curl_close($saleConn);

                $response = json_decode($sale);

                 // fetch artish PayPal/Venmo ID.
                 $transUpdate = Transaction::where('id', '=', $input['trn_id'])->first();
                 $artistWallet = ArtistWallet::where('artist_id', '=', $input['artist_id'])->first();

                 $postRequest = array(
                     "sender_batch_header" => array(
                         "sender_batch_id" => mt_rand(100000, 999999),
                         "recipient_type" => "EMAIL",
                         "email_subject" => "You have money!",
                         "email_message" => "You received a payment. Thanks for using our service!"
                         ),
                     "items" => array(
                         array(
                             "amount" => array(
                                "value" =>  $transUpdate->user_amount,
                                "currency" => "USD"
                             ),
                             "sender_item_id" => mt_rand(100000, 999999),
                             "recipient_wallet" => $artistWallet->wallet_type,
                             "receiver" => $artistWallet->walled_id
                         ),
                     )
                 );

                 $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payouts');
                 curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                     "Authorization: Bearer $token",
                     'Content-Type:application/json'
                 ));
                 curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
                 curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                 $apiResponse = curl_exec($cURLConnection);
                 curl_close($cURLConnection);

                 $apiResponse = json_decode($apiResponse);

                 $transUpdate->trxn_id = $sale_id;
                 $transUpdate->payer_id = $input['PayerID'];
                 $transUpdate->is_success = 1;
                 $transUpdate->is_redemed = 1;
                 $transUpdate->updated_at = Carbon::now();
                 $transUpdate->save();

                // send notification

                $send_to  = User::where('id', '=', $input['artist_id'])->first();

                $fcm_token = $send_to->fcm_token;
                $message = 'Congratulations! Your amazing fans have sent you a tip';

                $data = array(
                    'sender_id' => $send_to->id,
                    'opponent_id' => $send_by->id,
                    'title'   => 'Tip Received',
                    'message' => $message
                );

                $this->pushNotification($fcm_token, 'message_user_sent', $message, $data);

                 return view('success');
            }


        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            $this->error('Something went wrong at the server end.',[]);
        }
    }

    public function paymentFormWeb(Request $request) {
        try {

            $amount = (float)$request->query('amount');
            $device_id = $request->query('device_id');
            $artist_name = ucwords($request->query('artist_name'));
            $album_name = ucwords($request->query('album'));
            $user_id = $request->query('user_id');

            // check artist exits
            $term = strtolower($artist_name);
            $artistCheck = User::whereRaw('lower(name) like (?)',["%{$term}%"])->first();

            if(is_null($artistCheck)) {
                $insertData = [
                    'name' => $artist_name,
                    'password' => bcrypt('123456'),
                    'profile_pic' => 'no-image.png',
                    'device_id' => 0,
                    'role_id' => 2,
                    'is_active' => 0,
                    'is_logged' => 0,
                    'fcm_token' => 'www'
                ];

                $artist_id = DB::table('users')->insertGetId($insertData);

                // Add artist Identity
                $insertSpotify = [
                    'unique_id' => $this->random_strings(4),
                    'artist_id' => $artist_id
                ];
                $insertSpotData = DB::table('artist_identity')->insertGetId($insertSpotify);

                $intent = 'authorize';

                // Add Song
                $artistSong = new ArtistSong();
                $artistSong->artist_id = $artist_id;
                $artistSong->song = $album_name;
                $artistSong->created_at = carbon::now();
                $artistSong->save();

            } else {
                $checkWallet = ArtistWallet::where('artist_id', '=', $artistCheck->id)->first();
                if(is_null($checkWallet)) {
                    $artist_id = $artistCheck->id;
                    $intent = 'authorize';
                } else {
                    $artist_id = $artistCheck->id;
                    $intent = 'sale';
                }

            }

            return view('welcomeWeb', compact(
                'amount',
                'device_id',
                'artist_id',
                'intent',
                'artist_name',
                'user_id'
            ));


        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function createPaymentWeb(Request $request) {
        try {
            $input = $request->post();

            $token = $this->generateToken();

            $karmaPoints  = (float)$input['amount'] * 20;

            if($input['user_id'] != 0) {
                $checkData = KarmaPoint::where('user_id'.'=', $input['user_id'])->first();
                if(is_null($checkData)) {
                    // insert data
                    $ins = new KarmaPoint();
                    $ins->user_id = $input['user_id'];
                    $ins->kPoints = $karmaPoints;
                    $ins->created_at = Carbon::now();
                    $ins->save();
                } else {
                    // update data
                    $checkData->user_id = $input['user_id'];
                    $checkData->kPoints = $karmaPoints;
                    $checkData->updated_at = Carbon::now();
                    $checkData->save();
                }

                if($input['tranType'] === 'sale') {
                    // create tranaction
                   $transaction = new Transaction();
                   $transaction->device_id = $input['user_id'];
                   $transaction->total_amount = $input['amount'];
                   $transaction->user_amount = ($input['amount']/100)*85;
                   $transaction->admin_amount = ($input['amount']/100)*15;
                   $transaction->artist_id = $input['artist_id'];
                   $transaction->created_at = Carbon::now();
                   $transaction->is_success = 0;
                   $transaction->is_redemed = 0;
                   $transaction->save();
                   $trn_id = $transaction->id;
                   $type = 'sale';

               } else if($input['tranType'] === 'authorize') {
                   $transaction = new AuthPayment();
                   $transaction->device_id = $input['user_id'];
                   $transaction->amount = $input['amount'];
                   $transaction->artist_id = $input['artist_id'];
                   $transaction->created_at = Carbon::now();
                   $transaction->save();

                   $trn_id = $transaction->id;

                   $type = 'authorize';
               }


            } else {
                // add points to clients Table
                $deviceUpdate = Device::where('id', '=', $input['device_id'])->first();
                // dd($deviceUpdate);
                $deviceUpdate->kPoints = $karmaPoints;
                $deviceUpdate->updated_at = Carbon::now();
                $deviceUpdate->save();
                if($input['tranType'] === 'sale') {
                    // create tranaction
                   $transaction = new Transaction();
                   $transaction->device_id = $input['device_id'];
                   $transaction->total_amount = $input['amount'];
                   $transaction->user_amount = ($input['amount']/100)*85;
                   $transaction->admin_amount = ($input['amount']/100)*15;
                   $transaction->artist_id = $input['artist_id'];
                   $transaction->created_at = Carbon::now();
                   $transaction->is_success = 0;
                   $transaction->is_redemed = 0;
                   $transaction->save();
                   $trn_id = $transaction->id;
                   $type = 'sale';

               } else if($input['tranType'] === 'authorize') {
                   $transaction = new AuthPayment();
                   $transaction->device_id = $input['device_id'];
                   $transaction->amount = $input['amount'];
                   $transaction->artist_id = $input['artist_id'];
                   $transaction->created_at = Carbon::now();
                   $transaction->save();

                   $trn_id = $transaction->id;

                   $type = 'authorize';
               }
            }

           $artist_name = $input['artist_name'];

           $returnUrl = "https://www.dapperitmedia.com/api/payment/success/web?type=$type&artist_id=".$input['artist_id']."&kpoints=$karmaPoints&trn_id=$trn_id";

            $postRequest = array(
                "intent" => $input['tranType'],
                "payer" => array (
                    "payment_method" =>  "paypal"
                ),
                "transactions" => array(
                    array(
                        "amount" => array(
                            "total" =>  $input['amount'],
                            "currency" => "USD"
                        )
                    )
                ),
                "redirect_urls" => array(
                    "return_url" => $returnUrl,
                    "cancel_url" => "https://www.dapperitmedia.com/api/payment/faliure/web"
                )
            );


            $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payment');
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $token",
                        'Content-Type:application/json'
            ));
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);

            $apiResponse = json_decode($apiResponse);
            // dd($apiResponse);

            return $apiResponse->links[1]->href;

        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            return Redirect::to("https://tipartists.com/home?type=error");
        }
    }

    public function failiurePageWeb() {
        return Redirect::to("https://tipartists.com/home?type=error");
    }


    public function successPageWeb(Request $request) {
        try {
            $input = $request->query();
            $token = $this->generateToken();

            if($input['type'] === 'authorize') {

                  // Execute Payment
                    $executePost = array(
                        'payer_id' => $input['PayerID']
                    );

                    $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payment/'.$input['paymentId'].'/execute');
                    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $token",
                            'Content-Type:application/json'
                    ));
                    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($executePost));
                    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec($cURLConnection);
                    curl_close($cURLConnection);

                    $response = json_decode($response);

                    //Authorise Payment

                    $auth_id = $response->transactions[0]->related_resources[0]->authorization->id;

                    $auth_link = $response->transactions[0]->related_resources[0]->authorization->links[0]->href;

                    $saleConn = curl_init($auth_link);
                    curl_setopt($saleConn, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $token",
                            'Content-Type:application/json'
                    ));
                    curl_setopt($saleConn, CURLOPT_RETURNTRANSFER, true);

                    $sale = curl_exec($saleConn);
                    curl_close($saleConn);

                    $sale = json_decode($sale);


                    $authUpdate = AuthPayment::where('id', '=', $input['trn_id'])->first();
                    $authUpdate->auth_id = $auth_id;
                    $authUpdate->payer_id = $input['PayerID'];
                    $authUpdate->updated_at = Carbon::now();
                    $authUpdate->save();

                    $artistData = User::where('id', $input['artist_id'])->first();
                    $artist_name =  $artistData->name;

                    $data = array(
                        'name' => $artist_name
                    );

                    Mail::send('emails.NewTip',$data, function($m)use($artist_name) {
                        $m->to(['mark@tipartists.com','anuva.kataria@imarkinfotech.com'])
                            ->subject(" New Artist Tipped");
                    });

                    return Redirect::to("https://tipartists.com/thank-you?type=".$input['type']."&artist_id=".$input['artist_id']."&kpoints=".$input['kpoints']."&trn_id=". $input['trn_id'].'&artist_name='.$artist_name);

            } else if($input['type'] === 'sale') {
                 // Execute Payment
                 $executePost = array(
                    'payer_id' => $input['PayerID']
                );

                $execConnection = curl_init('https://api.paypal.com/v1/payments/payment/'.$input['paymentId'].'/execute');
                curl_setopt($execConnection, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $token",
                        'Content-Type:application/json'
                ));
                curl_setopt($execConnection, CURLOPT_POSTFIELDS, json_encode($executePost));
                curl_setopt($execConnection, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($execConnection);
                curl_close($execConnection);

                $response = json_decode($response);

                $sale_id = $response->transactions[0]->related_resources[0]->sale->id;
                $sale_link = $response->transactions[0]->related_resources[0]->sale->links[0]->href;

                // Capture Payment
                $saleConn = curl_init($sale_link);
                curl_setopt($saleConn, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer $token",
                        'Content-Type:application/json'
                ));
                curl_setopt($saleConn, CURLOPT_RETURNTRANSFER, true);

                $sale = curl_exec($saleConn);
                curl_close($saleConn);

                $response = json_decode($sale);

                 // fetch artish PayPal/Venmo ID.
                 $transUpdate = Transaction::where('id', '=', $input['trn_id'])->first();
                 $artistWallet = ArtistWallet::where('artist_id', '=', $input['artist_id'])->first();

                 $postRequest = array(
                    "sender_batch_header" => array(
                        "sender_batch_id" => mt_rand(100000, 999999),
                        "email_subject" => "You have money!",
                        "email_message" => "You received a payment. Thanks for using our service!"
                        ),
                    "items" => array(
                        array(
                            "recipient_type" => "EMAIL",
                            "amount" => array(
                                    "value" =>  $transUpdate->user_amount,
                                    "currency" => "USD"
                            ),
                            "note" => "Thanks for your patronage!",
                            "sender_item_id" => mt_rand(100000, 999999),
                            "receiver" => $artistWallet->walled_id
                        )
                    )
                );

                 $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payouts');
                 curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                     "Authorization: Bearer $token",
                     'Content-Type:application/json'
                 ));
                 curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
                 curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                 $apiResponse = curl_exec($cURLConnection);
                 curl_close($cURLConnection);

                 $apiResponse = json_decode($apiResponse);

                //  dd($apiResponse);

                 $transUpdate->trxn_id = $sale_id;
                 $transUpdate->payer_id = $input['PayerID'];
                 $transUpdate->is_success = 1;
                 $transUpdate->is_redemed = 1;
                 $transUpdate->updated_at = Carbon::now();
                 $transUpdate->save();

                 // Send email
                $artistEmail = User::where('id','=', $input['artist_id'])->first();
                $artist_name =  $artistEmail->name;
                $data = array(
                    'name' => $artistEmail->name
                );

                Mail::send('emails.tipped',$data, function($m)use($artistEmail) {
                    $m->to($artistEmail->email)
                        ->subject("You have been tipped!");
                });

                return Redirect::to("https://tipartists.com/thank-you?type=".$input['type']."&artist_id=".$input['artist_id']."&kpoints=".$input['kpoints']."&trn_id=".$input['trn_id'].'&artist_name='.$artist_name);
            }


        } catch( \Exception $e) {
            // $this->error($e->getMessage(),[]);
            // $this->error('Something went wrong at the server end.',[]);
            return Redirect::to("https://tipartists.com/home?type=error");
        }
    }

    public function pointsReedem(Request $request) {
        try {

            $token = $request->bearerToken();
            $user = JWTAuth::toUser($token);
            $input = $request->post();

            // add Wallet
            $wallet = ArtistWallet::where('artist_id', '=', $user->id)->first();


            // Transfer Payment
            $postRequest = array(
                "sender_batch_header" => array(
                    "sender_batch_id" => mt_rand(100000, 999999),
                    "email_subject" => "You have money!",
                    "email_message" => "You received a payment. Thanks for using our service!"
                    ),
                "items" => array(
                    array(
                        "recipient_type" => "EMAIL",
                        "amount" => array(
                            "value" =>  $input['amount'],
                            "currency" => "USD"
                        ),
                        "note" => "Thanks for your patronage!",
                        "sender_item_id" => mt_rand(100000, 999999),
                        "receiver" => $wallet->walled_id
                    )
                )
            );

            $token = $this->generateToken();

            $cURLConnection = curl_init('https://api.paypal.com/v1/payments/payouts');
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $token",
                'Content-Type:application/json'
            ));
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);

            $apiResponse = json_decode($apiResponse);

            //update transaction table.
            $transaction = Transaction::where('artist_id','=', $user->id)->where('is_redemed', '=', 0)->where('is_success', '=', 1)->get();

            foreach($transaction as $ts) {
                $updateTran = Transaction::where('id','=', $ts->id)->first();
                $updateTran->is_redemed = 1;
                $updateTran->updated_at = Carbon::now();
                $updateTran->save();
            }

            $this->success('Points reedemed successfully', $apiResponse);

        } catch( \Exception $e) {
            // redirect to success Page
           $this->error($e->getMessage(),[]);
       }

    }

    public function latestFundReceived(Request $request) {
        try {
            $token = $request->bearerToken();
            $user = JWTAuth::toUser($token);


            $paymentToken = $this->generateToken();
            $authPayment = AuthPayment::where('artist_id', $user->id)->where('is_captured', '=', 0)->get();


            if(count($authPayment) > 0) {
                foreach($authPayment as $ap) {
                    $day29 = date('Y-m-d', strtotime($ap->created_at. ' + 29 days'));
                    $day4 = date('Y-m-d', strtotime($ap->created_at. ' + 4 days'));
                    $currentDate = date('Y-m-d');
                    if($day29 >= $currentDate && $day4 <= $currentDate) {
                        $postRequest = array(
                            "amount" => array(
                            "value" => $ap->amount,
                            "currency_code" => "USD"
                            )
                        );

                        $cURLConnection = curl_init('https://api.paypal.com/v1/payments/authorization/'.$ap->auth_id.'/reauthorize');
                        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $paymentToken",
                            'Content-Type:application/json'
                        ));
                        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                        $apiResponse = curl_exec($cURLConnection);
                        curl_close($cURLConnection);



                        $apiResponse = json_decode($apiResponse);

                        // update the token in authorisation table
                        $authUpdate = AuthPayment::where('id', '=', $ap->id)->first();

                        $authUpdate->auth_id = $apiResponse->id;
                        $authUpdate->created_at = carbon::now();
                        $authUpdate->updated_at = carbon::now();
                        $authUpdate->save();

                        $cURLConnection = curl_init('https://api.paypal.com/v1/payments/authorization/'.$ap->auth_id.'/capture');
                        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $paymentToken",
                            'Content-Type:application/json'
                        ));
                        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                        $capturePayment = curl_exec($cURLConnection);
                        curl_close($cURLConnection);

                        $capturePayment = json_decode($capturePayment);


                        if(isset($capturePayment->state) && $capturePayment->state === 'completed') {
                            $cURLConnection = curl_init($capturePayment->links[0]->href);
                            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                                "Authorization: Bearer $paymentToken",
                                'Content-Type:application/json'
                            ));

                            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                            $payment = curl_exec($cURLConnection);
                            curl_close($cURLConnection);

                            $payment = json_decode($payment);
                            $transaction = new Transaction();
                            $transaction->device_id = $ap->device_id;
                            $transaction->trxn_id = $payment->id;
                            $transaction->total_amount = $payment->amount->total;
                            $transaction->user_amount = ($payment->amount->total/100)*85;
                            $transaction->admin_amount = ($payment->amount->total/100)*15;
                            $transaction->payer_id = $ap->payer_id;
                            $transaction->artist_id = $ap->artist_id;
                            $transaction->created_at = Carbon::now();
                            $transaction->is_success = 1;
                            $transaction->save();


                            // update message table
                            $message = Message::where('tran_id', '=', $ap->id)->where('sale_type', '=', 'authorize')->first();
                            if(!is_null($message)) {
                                $message->tran_id = $transaction->id;
                                $message->sale_type = 'sale';
                                $message->updated_at = Carbon::now();
                                $message->save();
                            }
                        }

                    } else {
                        $postRequest = array(
                            "amount" => array(
                                "currency" => "USD",
                                "total" => $ap->amount

                            ),
                            "final_capture" => true
                        );



                        $cURLConnection = curl_init('https://api.paypal.com/v1/payments/authorization/'.$ap->auth_id.'/capture');
                        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                            "Authorization: Bearer $paymentToken",
                            'Content-Type:application/json'
                        ));
                        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                        $capturePayment = curl_exec($cURLConnection);
                        curl_close($cURLConnection);
                        // dd($capturePayment);
                        $capturePayment = json_decode($capturePayment);


                        if(isset($capturePayment->state) && $capturePayment->state === 'completed') {

                            $cURLConnection = curl_init($capturePayment->links[0]->href);
                            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                                "Authorization: Bearer $paymentToken",
                                'Content-Type:application/json'
                            ));

                            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                            $payment = curl_exec($cURLConnection);
                            curl_close($cURLConnection);

                            $payment = json_decode($payment);

                            // update the token in authorisation table
                            $authUpdate = AuthPayment::where('id', '=', $ap->id)->first();

                            $authUpdate->is_captured = 1;
                            $authUpdate->created_at = carbon::now();
                            $authUpdate->updated_at = carbon::now();
                            $authUpdate->save();

                            $transaction = new Transaction();
                            $transaction->device_id = $ap->device_id;
                            $transaction->trxn_id = $payment->id;
                            $transaction->total_amount = $payment->amount->total;
                            $transaction->user_amount = ($payment->amount->total/100)*85;
                            $transaction->admin_amount = ($payment->amount->total/100)*15;
                            $transaction->payer_id = $ap->payer_id;
                            $transaction->artist_id = $ap->artist_id;
                            $transaction->created_at = Carbon::now();
                            $transaction->is_success = 1;
                            $transaction->save();

                            // update message table
                            $message = Message::where('tran_id', '=', $ap->id)->where('sale_type', '=', 'authorize')->first();
                            if(!is_null($message)) {
                                $message->tran_id = $transaction->id;
                                $message->sale_type = 'sale';
                                $message->updated_at = Carbon::now();
                                $message->save();
                            }

                        }
                    }
                }
            }

            $points = Transaction::where('artist_id', $user->id)->where('is_success', 1)->where('is_redemed', 0)->sum('user_amount');

            $this->success('last point', round($points, 2));

       } catch(\Exception $e) {
            $this->error($e->getMessage());
           $this->error('Something went at the server end. Please check with our support team.');
       }
    }

    public function artistFundReceived(Request $request) {
        try {

            $token = $request->bearerToken();

            // dd($token);
            $user = JWTAuth::toUser($token);

            $points = Transaction::where('artist_id', $user->id)->where('is_success', 1)->orderBy('created_at', 'desc')->get();

            $totalPoints = Transaction::where('artist_id', $user->id)->where('is_success', 1)->sum('user_amount');

            $redeemedPoints = Transaction::where('is_redemed','=',1)->where('artist_id', $user->id)->where('is_success', 1)->sum('user_amount');

            $pendingPoints = Transaction::where('is_redemed','=',0)->where('artist_id', $user->id)->where('is_success', 1)->sum('user_amount');

            $resultArray = array (
                'totalPoints' => round($totalPoints, 2),
                'redeemedPoints' => round($redeemedPoints, 2),
                'pendingPoints' => round($pendingPoints, 2),
                'list' => array()

            );
            foreach($points as $p) {
                $userName = User::where('device_id', '=', $p->device_id)->where('role_id', '=', 1)->first();
                // dd($userName);
                $resultArray['list'] [] = array(
                    'user_name' => is_null($userName)?'Guest':$userName->name,
                    'user_id' => is_null($userName)? 0:$userName->id,
                    'created_at' => date('d/m/Y', strtotime($p->created_at)),
                    'amount' => $p->user_amount,
                    'trn_id' => $p->id
                );
            }

            $this->success('last point', $resultArray);

        } catch(\Exception $e) {
            //  $this->error($e->getMessage());
            $this->error('Something went at the server end. Please check with our support team.');
        }

    }

    public function reAuthorisePayment() {
        $token = $this->generateToken();

        // fetch all authorised data
        $authPayment = AuthPayment::all();

        foreach($authPayment as $auth) {
            $day27 = date('Y-m-d', strtotime($auth->created_at. ' + 27 days'));
            $currentDate = date('Y-m-d');

            if($day27 === $currentDate) {
                // reauthorise at 27th day
                $postRequest = array(
                        "amount" => array(
                          "value" => $auth->amount,
                          "currency_code" => "USD"
                        )
                );
                $cURLConnection = curl_init('https://api.paypal.com/v1/payments/authorization/'.$auth->auth_id.'/reauthorize');
                curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                    "Authorization: Bearer $token",
                    'Content-Type:application/json'
                ));
                curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                $apiResponse = curl_exec($cURLConnection);
                curl_close($cURLConnection);

            }

        }

        return true;

    }

    public function generateToken() {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://api.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_POSTFIELDS,"grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            // 'dev Authorization: Basic QWJqQVl4Qlh0RklYdGZCUVNQY1VkVzNYcmEtODI3VjNNMXpYdFFOZHBhcTR4b0FMTFUwUGgzbUxFS3YtN3ZuOF9URGF5bmJlb0pha0daTks6RUUzRnpyQjNMck5feDY0RDgwaVFSRm5palF3a2RwVnlZTXgyRFN2YjA3blNPZXNoeGQ5VFRnMG4yMElkUEZERmFmYTFURXc0eTJTUGlDNzE='
            'Authorization: Basic QWJCd0NyNVk3d3NSZ1Q3SW95NWdBVkdVUXF6Z1h2TDNJbEEtZDdmUk9Xc1c1RGtXbHMyNUwxX2pDOTUySUMzbmZ6dzFnRlExWjB4c2dXcTQ6RU9KUG1DNHRLQ25OcXllZzMwazdkYlF0em1DMzNxQ0xCd3QzWDJlQ3ZvM0VfVDRJenlvMTRZYllwdm1UeUtIYXVhVDkyQUxnZjJ2aEVlemY='
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        $result = json_decode($server_output);
        return $result->access_token;

    }
}
