@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <!-- 头像区 -->
                <div class="media">
                    <a href="#" class="pull-left">
                        <img src="http://www.runoob.com/try/bootstrap/layoutit/v3/default7.jpg" class="media-object" alt='' />
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading">{{ Auth::user()->name }}</h4>
                    </div>
                </div>
                <!-- 头像区结束 -->
                <br/>
                <!-- 选项卡 -->
                <div class="tabbable" id="tabs-159759">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#panel-957750" data-toggle="tab">会话</a>
                        </li>
                        <li>
                            <a href="#panel-965648" data-toggle="tab">通讯录</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        
                        <div class="tab-pane" id="panel-965648">
                            <p>
                                Howdy, I'm in Section 2.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- 选项卡结束 -->
            </div>
            <div class="col-md-9">
                <!-- 聊天面板 -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title" id="chat-title">
                            公共聊天室
                        </h3>
                    </div>
                    <div class="panel-body" style="height:500px" id="chat-main">

                    </div>
                    <div class="panel-footer">
                        <input type="text" style="width:92%; background-color:transparent; border:0" placeholder="在此处输入" id="input-msg"><button id="btn-send">发送</button>
                    </div>
                </div>
                <!-- 聊天面板结束 -->
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    //获取ajax Token
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-XSRF-Token': $('meta[name="_token"]').attr('content')
            }
        });
    });

    //发送消息到公共聊天室
    $("#btn-send").click(function() {
        $.ajax({
            type: "POST",
            url: "{{ action('HomeController@publicSend') }}",
            data: {
                msg: $("#input-msg").val(),
            },
            dataType: "text",
            success: function(data){
                console.log(data);
            },
            error: function(jqXHR){
                alert("发生错误：" + jqXHR.status);
            },
        });
    });
</script>
@endsection