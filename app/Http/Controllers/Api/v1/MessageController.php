<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Message;
use App\Model\User;
use Carbon\Carbon;
use DB;
use JWTAuth;

class MessageController extends Controller
{
    public function sendMessage(Request $request) {
        try {
            $input = $request->post();

            $message = new Message();
            $message->sender_id = $input['sender_id'];
            $message->opponent_id = $input['opponent_id'];
            $message->tran_id = $input['trn_id'];
            $message->sale_type = isset($input['sale_type'])? 'authorize': $input['sale_type'];
            $message->message = $input['message'];
            $message->created_at = Carbon::now();
            $message->save();

            // send notification 
            $send_to  = User::where('id', '=', $input['opponent_id'])->first();
            $send_by = User::where('id', '=', $input['sender_id'])->first();

            // $fcm_token = $send_to->fcm_token;
            // $message = $send_by->name.' has tiped and sent you a message.';

            // $data = array(
            //     'sender_id' => $input['sender_id'],
            //     'opponent_id' => $input['opponent_id'],
            //     'title'   => 'New Message Received',
            //     'message' => $message
            // );

            // $this->pushNotification($fcm_token, 'message_user_sent', $message, $data);
            $this->success('Message sent succesfully',[]);
        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function artistSendMessage(Request $request) {
        try {

            $input = $request->post();

            $message = Message::where('id', $input['message_id'])->first();

            $message->revert = $input['message'];
            $message->updated_at = Carbon::now();
            $message->save();

             // send notification 
            $send_by  = User::where('id', '=', $message->opponent_id)->first();
            $send_to  = User::where('id', '=', $message->sender_id)->first();
 
            // $fcm_token = $send_to->fcm_token;
            // $message = $send_by->name.' has sent you a message.';
 
            // $data = array(
            //     'sender_id' => $send_to->id,
            //     'opponent_id' => $send_by->id,
            //     'title'   => 'New Message Received',
            //     'message' => $message
            // );
 
            // $this->pushNotification($fcm_token, 'message_user_sent', $message, $data);

            $this->success('Message sent succesfully',[]);

        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function messageView(Request $request) {
        try {

            $input = $request->post();

            $messageList = DB::table('messages')
            ->leftJoin('users as u', 'u.id', 'messages.sender_id')
            ->where('messages.sender_id', $input['sender_id'])
            ->where('messages.opponent_id', $input['opponent_id'])
            ->where('messages.tran_id', $input['trn_id'])
            ->get(['messages.created_at', 'messages.updated_at','u.name', 'messages.message','messages.id', 'messages.revert']);


            $resultArray = array(
                'created_at' => Carbon::parse($messageList[0]->created_at)->format('d M'),
                'updated_at' => Carbon::parse($messageList[0]->updated_at)->format('d M'),
                'message' => strtoupper($messageList[0]->message),
                'name' => $messageList[0]->name,
                'id' => $messageList[0]->id,
                'revert' => $messageList[0]->revert != null?strtoupper($messageList[0]->revert): $messageList[0]->revert
            );
            
            $this->success('Message list',$resultArray);

        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }


    public function messageList(Request $request) {
        try {

            $token = $request->bearerToken();
            $user = JWTAuth::toUser($token);

            $input = $request->post();

            if($input['type'] == 1) {
                $messageList = DB::table('messages')
                ->leftJoin('users as u', 'u.id', 'messages.opponent_id')
                ->where('messages.sender_id', $user->id)
                ->orderBy('messages.updated_at','DESC')
                ->get([ 'messages.*','u.name']);
            } else {
                $messageList = DB::table('messages')
                ->leftJoin('users as u', 'u.id', 'messages.sender_id')
                ->where('messages.opponent_id', $user->id)
                ->orderBy('messages.updated_at','DESC')
                ->get(['u.name', 'messages.*']);
            }

            $resultArray = [];

            foreach ($messageList as $ml) {
                $resultArray[] = array(
                    'name' => $ml->name,
                    'id' => $ml->id,
                    'created_at' => Carbon::parse($ml->created_at)->format('d M'),
                    'updated_at' => Carbon::parse($ml->updated_at)->format('d M'),
                    'sender_id' => $ml->sender_id,
                    'opponent_id' => $ml->opponent_id,
                    'tran_id' =>$ml->tran_id,
                    'sale_type' => $ml->sale_type,
                    'message' => strtoupper($ml->message),
                    'revert' => strtoupper($ml->revert)
                );
            }

            $this->success('Message list',$resultArray);

        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function messageAll(Request $request) {
        try {

            $input = $request->post();

            $messageList = DB::table('messages')
            ->leftJoin('users as u', 'u.id', 'messages.sender_id')
            ->leftJoin('users as a', 'a.id', 'messages.opponent_id')
            ->where('messages.sender_id', $input['sender_id'])
            ->get(['messages.created_at', 'u.name as user_name', 'messages.message','messages.id', 'a.name as artist_name']);

            $resultArray = [];

            foreach ($messageList as $ml) {
                $resultArray[] = array(
                    'created_at' => Carbon::parse($ml->created_at)->format('d M'),
                    'message' => strtoupper($ml->message),
                    'user_name' => $ml->user_name,
                    'artist_name' => $ml->artist_name,
                    'id' => $ml->id,
                    'revert' => strtoupper($ml->revert)
                );
            }

            $this->success('Message list',$resultArray);

        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

}
