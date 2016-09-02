<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Requests;

class FriendsController extends Controller
{
    /**
     * 好友请求
     */
    public function firendRequest(Request $request) {
        $id = $request->input('id');
        DB::table('request')->insert([
            [
                'from' => Auth::user()->id,
                'to' => $id,
                'status' => 0,
            ]
        ]);
        echo '请求已发出，请耐心等待';
    }

    /**
     * 好友列表
     */
    public function listFriends() {
        $list = DB::table('friends')->where('user', Auth::user()->id)->get();
        return $list;
    }

    /**
     * 同意好友请求
     * @param Request $request
     */
    public function agree(Request $request) {
        $id = $request->input('id');
        DB::table('friends')->where('id', $id)->update(['status'=>1]);
        
    }
}
