<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function setResponse($response = [], $status = 200)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }

    /* if operation successfully performed
     * @param  int $msg  Message t obe displayed
     * @param  array  $data data to return with success message
     * @return callback
     */

    protected function success($message = "Success", $responseData = [], $status = 200)
    {
        $response = [];
        $response['code'] = $status;
        $response['success'] = true;
        $response['message'] = $message;
        $response['data'] = $responseData;
        return $this->setResponse($response, $status);
    }

    /**
     * If operation was'nt performed successfully
     * @param  string $error Error Message
     * @return callback
     */
    public function error($message = "Error occured.", $responseData = null, $status = 400)
    {
        $response = [];
        $response['code'] = $status;
        $response['success'] = false;
        $response['message'] = $message;
        $response['data'] = $responseData;
        return $this->setResponse($response, $status);
    }

    public function validation($request = [], $rules = [], $messages = [])
    {
        $validator = Validator::make($request, $rules, $messages);
        if ($validator->fails()) {

            $this->error(@$validator->errors()->all()[0]);
        }
    }

    public function numberFormat($num) {
        if($num>1000) {
              $x = round($num);
              $x_number_format = number_format($x);
              $x_array = explode(',', $x_number_format);
              $x_parts = array('k', 'm', 'b', 't');
              $x_count_parts = count($x_array) - 1;
              $x_display = $x;
              $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
              $x_display .= $x_parts[$x_count_parts - 1];
              return $x_display;
        }
            return $num;
      }

      public function imageUpload($data, $folder_path)
    {

        $image_parts = explode(";base64,", $data);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_base64 = base64_decode($image_parts[1]);
        $file_name = 'user-'.uniqid().'.'.$image_type_aux[1];
        $file = $folder_path.$file_name;
        file_put_contents($file, $image_base64);
        return $file_name;
    }

    public function pushNotification($fcm_token, $noti_type, $message, $data) {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $Server_token='AAAA45SIkto:APA91bFVJCeOenzZQTyE_s7QJk_ysVPJ1b5nDxQiwIDa7CTXNETTBOc-JCu0gDM4HVssZ0IJMgenf0Div-yRHcdw0CSdPsMbXiEq_-IJrA7i049oCkmw_j3vUqdK1e3g1dDMhDD5nBCc';

        $notification = [
            'body' => $message,
            'sound' => true,
        ];
        $extraNotificationData = [ "notification_foreground" => "true","moredata" => $data, "message" => $message, "title" => $data['title'] ,'notification_type' => $noti_type ];

        $fcmNotification = [
            'to'        =>  $fcm_token,
            'notification' => $notification,
            'data' => $extraNotificationData,
            "priority" => "high"
        ];

        $headers = [
            'Authorization: key='.$Server_token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);

        curl_close($ch);
    }

    public function random_strings($length_of_string) {
        // $str_result = '0123456789';
        return mt_rand(1000, 9999);
    }

    public function randomPass($length_of_string) {
        // $str_result = '0123456789';
        return mt_rand(1000000, 99999999);
    }
}
