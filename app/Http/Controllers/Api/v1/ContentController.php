<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ContactUS;
use App\Model\Content;
use Carbon\Carbon;

class ContentController extends Controller
{
    public function getContent($slug) {
        try {

            $content = Content::where('slug', $slug)->first();

            $this->success('contentList', $content);

        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }

    public function contactUs(Request $request) {
        try {

           $input = $request->post();
            $rules = [
                'email' => 'required|email|max:255',
                'title' => 'required',
                'message' => 'required',
            ];
            
        $this->validation($input,$rules);

           $contactUs = new ContactUS();
           $contactUs->email = $input['email'];
           $contactUs->title = $input['title'];
           $contactUs->message = $input['message'];
           $contactUs->created_at = Carbon::now();
           $contactUs->save();

           $this->success('Your message has been sent to support team. We will contact you soon!', []);

        } catch(\Exception $e) {
            $this->error($e->getMessage(),[]);
        }
    }
}
