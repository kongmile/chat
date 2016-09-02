<?php
set_time_limit(0);
$sk=new Sock('127.0.0.1',8000);
$sk->run();

class Sock
{
    public $sockets;
    public $users;
    public $master;

    /**
     * Sock constructor.
     * @param $address
     * @param $port
     */
    public function __construct($address, $port) {
        $this->master = $this->webSockets($address, $port);
        $this->sockets = array('s' => $this->master);
    }

    public function run() {
        while(true) {
            $changes = $this->sockets;
            $write=NULL;
            $except=NULL;
            socket_select($changes,$write,$except,NULL);
            foreach ($changes as $sock) {
                if($sock == $this->master) {
                    $client = socket_accept($this->master);
                    if ($client < 0) {
                        // debug
                        $this->e("socket_accept() failed");
                        continue;
                    } else {
                        //connect($client);
                        $this->sockets[]=$client;
                        $this->users[]=array(
                            'socket'=>$client,
                            'handshake'=>false
                        );
                        $this->e("connect client\n");
                    }
                } else {
                    $len = @socket_recv($sock,$buffer,2048,0);
                    $k=$this->search($sock);
                    if (!$this->users[$k]['handshake']) {
                        // 如果没有握手，先握手回应
                        $this->doHandShake($k,$buffer);
                        $this->e("shakeHands\n");
                    } else {
                        $buffer = $this->uncode($buffer);
                        if($buffer==false){
                            continue;
                        }
                        $this->send($k,$buffer);
                    }
                }
            }
        }
    }

    function search($sock){
        foreach ($this->users as $k=>$v){
            if($sock==$v['socket'])
                return $k;
        }
        return false;
    }

    /**
     * 实例化sock
     * @param $address
     * @param $port
     * @return resource
     */
    private function webSockets($address, $port) {
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($server, $address, $port);
        socket_listen($server);
        $this->e('Server Started : '.date('Y-m-d H:i:s'));
        $this->e('Listening on   : '.$address.' port '.$port);
        return $server;
    }

    function e($str){
        $path=dirname(__FILE__).'/log.txt';
        $str=$str."\n";
        error_log($str,3,$path);
    }

    function dohandshake($k,$buffer){
        $buf  = substr($buffer,strpos($buffer,'Sec-WebSocket-Key:')+18);
        $key  = trim(substr($buf,0,strpos($buf,"\r\n")));

        $new_key = base64_encode(sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true));

        $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $new_message .= "Upgrade: websocket\r\n";
        $new_message .= "Sec-WebSocket-Version: 13\r\n";
        $new_message .= "Connection: Upgrade\r\n";
        $new_message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";

        socket_write($this->users[$k]['socket'],$new_message,strlen($new_message));
        $this->users[$k]['handshake']=true;
        return true;
    }

    function uncode($str){
        $mask = array();
        $data = '';
        $msg = unpack('H*',$str);
        $head = substr($msg[1],0,2);
        if (hexdec($head{1}) === 8) {
            $data = false;
        }else if (hexdec($head{1}) === 1){
            $mask[] = hexdec(substr($msg[1],4,2));
            $mask[] = hexdec(substr($msg[1],6,2));
            $mask[] = hexdec(substr($msg[1],8,2));
            $mask[] = hexdec(substr($msg[1],10,2));

            $s = 12;
            $e = strlen($msg[1])-2;
            $n = 0;
            for ($i=$s; $i<= $e; $i+= 2) {
                $data .= chr($mask[$n%4]^hexdec(substr($msg[1],$i,2)));
                $n++;
            }
        }
        return $data;
    }

    function code($msg){
        $msg = preg_replace(array('/\r$/','/\n$/','/\r\n$/',), '', $msg);
        $frame = array();
        $frame[0] = '81';
        $len = strlen($msg);
        $frame[1] = $len<16?'0'.dechex($len):dechex($len);
        $frame[2] = $this->ord_hex($msg);
        $data = implode('',$frame);
        return pack("H*", $data);
    }

    function ord_hex($data)  {
        $msg = '';
        $l = strlen($data);
        for ($i= 0; $i<$l; $i++) {
            $msg .= dechex(ord($data{$i}));
        }
        return $msg;
    }


    // 返回数据
    function send($k, $msg){
        $res = $this->code($k.': '.$msg);
        foreach($this->users as $v) {
            socket_write($v['socket'], $res, strlen($res));
        }
    }
}