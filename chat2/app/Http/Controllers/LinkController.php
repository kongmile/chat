<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;

class LinkController extends Controller
{
    public function link(Request $request) {
        set_time_limit(0);//无请求超时
        $max_id = DB::table('users')->where('id', Auth::user()->id)->value('public_max_id'); //数据库中用户原公共聊天室最大已读id
        if(!$max_id) $max_id = 0;
        $t = 0;//初始化时间计数
        while(true) {
            usleep(500000);//进程挂起0.5秒
            $t++;
            //现在数据库中消息条数
            $new_max_id = DB::table('public_msgs')->max('id');
            if(!$max_id) $max_id = 0;

            //公共聊天室有消息
            if($new_max_id > $max_id) { //如果现在的最大id大于以前的
                //获取消息
                $msgs = DB::table('public_msgs')->whereBetween('id', [$max_id+1, $new_max_id])
                    ->get();
                $res = [
                    'success' => true, //有新消息
                    'type' => 'public', //消息类型
                    'msgs' => $msgs, //消息数据数组
                ];
                //更新用户最大已阅id
                DB::table('users')
                    ->where('id', Auth::user()->id)
                    ->update(['public_max_id' => $new_max_id]);
                return response()->json(json_encode($res));
                break;
            }

            if($t == 25){
                $res = ['success' => false];
                return response()->json(json_encode($res));
                break;
            }
        }
    }
}
