<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Content;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
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
        $contentList = Content::all();
        return view('content.index',compact(
            'contentList'
        ));
    }

    public function add()
    {
        return view('content.create');
    }

    public function insert(Request $request)
    {
        $requestData = $request->post();

        $rules = array(
            'title' => 'required|min:3|max:50',
            'descrip' => 'required'
        );

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {

            $insertData = array(
                'title' => $requestData['title'],
                'slug' => str_replace(' ', '-', strtolower($requestData['title'])),
                'description' => $requestData['descrip'],
                'created_at' => carbon::now()
            );

            DB::table('content')->insert($insertData);

            return redirect()->to('/content')->with('success', 'Content inserted successfully!');
        }

    }

    public function edit($user_id)
    {
        $contentData = Content::find($user_id);
        return view('content.edit',compact(
            'contentData'
        ));
    }

    public function update(Request $request)
    {
        $requestData = $request->post();

        $rules = array(
            'title' => 'required|min:3|max:50',
            'descrip' => 'required'
        );

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {

            $user =Content::where('id',$requestData['content_id'])->first();
            $user->title = $requestData['title'] == '' || $requestData['title'] == null ? '':$requestData['title'];
            $user->description = $requestData['descrip'] == '' || $requestData['descrip'] == null ? '':$requestData['descrip'];
            $user->save();

            return redirect()->to('/content')->with('success', 'Content updated successfully!');
        }


    }

    public function delete($user_id) {
        $content = Content::find($user_id)->delete();
        return redirect()->to('/content')->with('success', 'Content deleted successfully!');
    }
}
