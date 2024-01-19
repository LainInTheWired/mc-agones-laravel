<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use App\Models\McServer;


class k8sAPIController extends Controller
{
    function getTestlogstream(Request $request){
        var_dump("start getTest");
        // return;
        // ddd("fejhwaio");
        // URL of the external API providing SSE
        $userid = "1";
        $podname = "mc-server-5xc46";
        $url = "http://10.200.0.191:8880/logstream?userid=" . $userid . "&podname=" . $podname;
        // cURLセッションを初期化
        $ch = curl_init();
        
        // cURLオプションを設定
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // 直接出力する
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/event-stream'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $buffer = '';

        // データ受信時のコールバック関数を設定
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$buffer) {
            $buffer .= $data;

            // 完全なSSEイベントが含まれているかチェック
            if (strpos($buffer, "\n\n") !== false) {
                // 完全なイベントを処理
                echo $buffer; // ここでブラウザにデータを出力
                ob_flush();
                flush();

                // バッファをクリア
                $buffer = '';
            }

            return strlen($data);
        });
        // ヘッダーを適切に設定（必要に応じて）
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: text/event-stream',
        ));
        
   
        
        // $i = 0;
        // // SSEストリームを開始
        // while (true) {

        //     $data = curl_exec($ch);
        //     ddd("fjewiajo");

        //     if ($data === false) {
        //         // エラー処理
        //         break;
        //     }

        //     // SSEデータを解析
        //     if (!empty($data)) {
        //         // ここで受信したデータを処理
        //         // 例: echo $data;
        //         ddd($data);
        //         var_dump($data);
        //     }
        //     ddd($data);


        //     // 必要に応じてsleepを入れる
        //     sleep(1); // 0.5秒待機
        //     $i++;
        // }

        // cURLセッションを閉じる
        curl_close($ch);



    }
    function getK8sPodlogStream(Request $request){
        $userid = 1;
        // $userid = Auth::id();
        // if($userid ==  null){
        //     return "error : no auth";
        // }
        $mc_server = new McServer;
        
        $records = $mc_server->where([["user_id","=",$userid],["name","=",$request["name"]]])->first();
        $podname = $records->pod_name;
        $url = "http://10.200.0.191:8880/logstream?userid=" . $userid . "&podname=" . $podname;
        return new StreamedResponse(function() use ($url) {
           // cURL セッションの初期化
           if (ob_get_level() > 0) {
                ob_end_clean();
            }
            $ch = curl_init($url);

            // cURL オプションの設定
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // ダイレクト出力
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
                echo $data;
                flush(); // バッファをフラッシュ
                return strlen($data); // 受け取ったデータの長さを返す
            });

            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            // cURL 接続を開始
            curl_exec($ch);

            // エラーチェック
            if (curl_errno($ch)) {
                echo "Error: " . curl_error($ch);
            }

            // cURL セッションを終了
            curl_close($ch);
        },200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);  

    }
    function getK8sStatusStream(Request $request){
        // ddd(Auth::id());
        $userid = "1";
        // $userid = Auth::id();
        // if($userid ==  null){
        //     return "error : no auth";
        // }
        $url = "http://10.200.0.191:8880/statusstream?userid=" . $userid;
        return new StreamedResponse(function() use ($url) {
            // Open the connection to the external SSE
            $handle = fopen($url, 'r');

            if (!$handle) {
                echo "Error: Unable to open the SSE stream.";
                return;
            }
            $mc_name = [];
            $mc_server = new McServer;
            $send = [];
            $i = 0;

            // Loop indefinitely to keep the connection open
            while (true) {
                if (connection_aborted()) {
                    break; // ループを抜ける
                }
                $data = fgets($handle);
                // Check if there is data
                if ($data !== false) {
                    // Relay the data received from the external SSE to the client
                    $trimmedData = trim($data);
                    if (!empty($trimmedData) && !str_starts_with($trimmedData, 'event:gsStatus')) { 
                        //最初の
                        $data = substr($data,5);
                        $data = json_decode($data, true);
                        // var_dump($data);

                        // ddd($data);            
                        foreach ($data as $items){
                            // var_dump($items["gameserver"]['metadata']['name']);
                            array_push($mc_name,$items["gameserver"]['metadata']['name']);
                        }
                        $records = $mc_server->whereIn('pod_name',$mc_name)->get();

                        foreach ($data as $items){
                            // var_dump("42");
                            var_dump($send);
                            $send[$i]["name"] = $items['gameserver']["metadata"]["labels"]["sname"];
                            foreach($records as $r){
                                $send[$i]["status"] = $items["pod"]['status']['phase'];
                                if($items["pod"]['status']['phase'] == "Runing"){
                                    if($items["gameserver"]['status']['state'] == "Ready"){
                                        $send[$i]["status"] = $items["pod"]['status']['phase'];
                                    }
                                    $send[$i]["status"] = $items["gameserver"]['status']['state'];
                                }
                                $send[$i]["domain"] = $items["gameserver"]["status"]["address"] . ":" .  $items["gameserver"]["spec"]["ports"][0]["hostPort"];
                
                                if($items["gameserver"]['metadata']['name'] == $r['pod_name']){
                                    $send[$i]["status"] = $items["pod"]['status']['phase'];
                                    // ddd(  $items["gameserver"]["spec"]["ports"][0]["hostPort"]);
                                    $send[$i]["domain"] = $items["gameserver"]["status"]["address"] . ":" .  $items["gameserver"]["spec"]["ports"][0]["hostPort"];
                                    $send[$i]["created_at"] = $r["created_at"];
                                }
                            }
                            if(!isset($send[$i]["created_at"])){
                                $send[$i]["status"] = "Terminating";
                            }
                            $i++;
                        }
                        $send = json_encode($send);
                        $send = "data:" . $send;
                        var_dump("変数send1:");
                        var_dump($send);
                        var_dump($trimmedData);

                        echo "{$send}\n\n";
                        $i = 0;
                        $send = [];
                    }
                 
                    // dd($handle);
                    // Flush the output buffer to the client
                    var_dump("before send!!!");
                    var_dump(ob_get_level());
                    if (ob_get_level() > 0) {
                        var_dump("send!!!!");
                        $i = 0;
                        ob_flush();
                    }
                    flush();
                    
                } else {
                    // No data received, sleep for a bit to avoid hammering the external server
                    // sleep(2);
                }
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);    
    }


    function getK8sStatus(Request $request){
        // ddd(Auth::id());
        $userid = Auth::id();
        if($userid ==  null){
            return "error : auth";
        }
        $url = "http://10.200.0.191:8880/status?userid=" . $userid;
        $responce = Http::get($url);
        $data = $responce ->json();
        $data = $data["gsStatus"];
        // ddd($data);
        $mc_name = [];
        foreach ($data as $items){
            array_push($mc_name,$items["gameserver"]['metadata']['name']);
        }
        // ddd($responce);
        $mc_server = new McServer;
        $records = $mc_server->whereIn('pod_name',$mc_name)->get();
        $send = [];
        $i = 0;

        foreach ($data as $items){
            $send[$i]["name"] = $items['gameserver']["metadata"]["labels"]["sname"];
            foreach($records as $r){
                $send[$i]["status"] = $items["pod"]['status']['phase'];
                if($items["pod"]['status']['phase'] == "Runing"){
                    if($items["gameserver"]['status']['state'] == "Ready"){
                        $send[$i]["status"] = $items["pod"]['status']['phase'];
                    }
                    $send[$i]["status"] = $items["gameserver"]['status']['state'];
                }
                $send[$i]["domain"] = $items["gameserver"]["status"]["address"] . ":" .  $items["gameserver"]["spec"]["ports"][0]["hostPort"];

                if($items["gameserver"]['metadata']['name'] == $r['pod_name']){
                    $send[$i]["status"] = $items["pod"]['status']['phase'];
                    // ddd(  $items["gameserver"]["spec"]["ports"][0]["hostPort"]);
                    $send[$i]["domain"] = $items["gameserver"]["status"]["address"] . ":" .  $items["gameserver"]["spec"]["ports"][0]["hostPort"];
                    $send[$i]["created_at"] = $r["created_at"];
                }
            }
            if(!isset($send[$i]["created_at"])){
                $send[$i]["status"] = "Terminating";
            }
            
            $i++;
        }
        // ddd($records);
        return $send;
    }
    function deleteGsPvc(Request $request){
        $userid=1;
        // $userid = Auth::id();
        // if($userid ==  null){
        //     return "error : auth";
        // }
        $name = $request['name'];
        $mc_server = new McServer;
        $records = $mc_server->where([['user_id', '=',$userid],['name','=',$name]])->first();
        $podname = $records['pod_name'];
        $pvcname = $records['pvc_name'];
        $url = "http://10.200.0.191:8880/delete?podname=" . $podname . "&pvcname=" . $pvcname;
        $responce = Http::GET($url);
        $mc_server->where([['user_id','=',$userid],['pod_name','=',$podname]])->delete();
        ddd($responce);
    }
}

