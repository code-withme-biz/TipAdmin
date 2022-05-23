<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{

    private $http_method = "POST";
	private $http_uri = "/v1/identify";
	private $data_type = "audio";
	private $signature_version = "1" ;
	private $path = "/public/music";
	private $ACR_HOST;
	private $ACR_ACCESS_KEY;
	private $ACR_ACCESS_SECRET;


    public function sendFile(Request $request){
        try {
            $this->getEnv();

            $input = json_decode($request->getContent(), true);

            // file_put_contents(public_path().'/test.txt', $request->getContent());

            $filePath = $this->convertJsonToFile($input['file'], $input['type']);
            return $this->recognitioRequest($filePath, $input['type']);
        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    private function recognitioRequest($filePath, $ext){
        try {
            //
            $timestamp = time();

            $string_to_sign = $this->http_method . "\n" .
                      $this->http_uri ."\n" .
                      $this->ACR_ACCESS_KEY . "\n" .
                      $this->data_type . "\n" .
                      $this->signature_version . "\n" .
                      $timestamp;

            $signature = hash_hmac("sha1", $string_to_sign, $this->ACR_ACCESS_SECRET, true);



            $signature = base64_encode($signature);

            $filesize =  filesize($filePath);

            // dd($filePath,$ext, 'audio.m4a');

            if($ext == 'audio/mp3') {
                $cfile = new \CURLFile($filePath,$ext, 'audio.mp3');
            } else if($ext == 'audio/m4a') {
                $cfile = new \CURLFile($filePath,$ext, 'audio.m4a');
            } else {
                $cfile = new \CURLFile($filePath,$ext, 'audio.wav');
            }



            $postfields = array(
                   "sample" => $cfile,
                   "sample_bytes"=>$filesize,
                   "access_key"=>$this->ACR_ACCESS_KEY,
                   "data_type"=>$this->data_type,
                   "signature"=>$signature,
                   "signature_version"=>$this->signature_version,
                   "timestamp"=>$timestamp);


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->ACR_HOST);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            // dd($result);

            if($result){
                return $this->success("Search Success",$result);
            }else{
                return curl_error($ch);
            }
        } catch( \Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    private function getEnv(){
        try {
            $this->ACR_HOST = env("ACR_HOST", "http://identify-us-west-2.acrcloud.com/v1/identify");
            $this->ACR_ACCESS_KEY = env("ACR_ACCESS_KEY", "9472e3067f1d224c12045450b4f2fe00");
            $this->ACR_ACCESS_SECRET = env("ACR_ACCESS_SECRET", "BZELpiqYbcJ8D6tDHMtHAzZOXDjaVw4NFrapmsZm");
        } catch (\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function convertJsonToFile($base64, $type) {

        if($type == 'audio/mp3' || $type == 'audio/3gpp') {
            file_put_contents(public_path().'/audio.mp3', base64_decode($base64));
            return public_path().'/audio.mp3';
        } else if($type == 'audio/m4a') {
            file_put_contents(public_path().'/audio.m4a', base64_decode($base64));
            return public_path().'/audio.m4a';
        } else {
            file_put_contents(public_path().'/audio.wav', base64_decode($base64));
            return public_path().'/audio.wav';
        }
    }

}
