<?php

namespace App\Http\Controllers;
use Auth;
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
     * 发送聊天室消息
     * @param Request $request
     */
    public function publicSend(Request $request) {
        //获取消息
        $message = \App\ChatMessage::create([
            'user_id' => Auth::user()->id,
            'message' => $request->input('msg')
        ]);

        //触发实验事件

        event(new \App\Events\ChatMessageWasReceived($message, Auth::user()));
        echo 'ChatRoom Message sent';
    }
}
