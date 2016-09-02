<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function test(Request $request) {
        var_dump(DB::table('msgs')->max('id'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * 发送消息
     * @param Request $request
     */
    public function send(Request $request) {
        $msg = $request->input('msg');
        $id = $request->input('id');
        DB::table('msgs')->insert([
            [
                'from' => Auth::user()->id,
                'to' => $id,
                'msg' => $msg,
                'status' => '0',
            ]
        ]);
        echo 'ChatRoom Message sent';
    }

    /**
     * 发送聊天室消息
     * @param Request $request
     */
    public function publicSend(Request $request) {
        $msg = $request->input('msg');
        DB::table('public_msgs')->insert([
            [
                'from' => Auth::user()->id,
                'msg' => $msg,
            ]
        ]);
        echo 'ChatRoom Message sent';
    }
}
