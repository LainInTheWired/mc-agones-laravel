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
    function getK8sStatusStream(Request $request){
        // ddd(Auth::id());
        $userid = "1";
        // $userid = Auth::id();
        // if($userid ==  null){
        //     return "error : no auth";
        // }
        $url = "http://192.168.11.6:8880/statusstream?userid=" . $userid;
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
                            // var_dump($send);
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
                    sleep(2);
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
        $url = "http://192.168.11.6:8880/status?userid=" . $userid;
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
        // ddd($data);
        // ddd($records);
        // foreach($records as $r){
        //     foreach ($data as $items){
        //         if($items["gameserver"]['metadata']['name'] == $r['pod_name']){
        //             $r["status"] = $items["pod"]['status']['phase'];
        //             // ddd(  $items["gameserver"]["spec"]["ports"][0]["hostPort"]);
        //             $r["domain"] = $items["gameserver"]["status"]["address"] . ":" .  $items["gameserver"]["spec"]["ports"][0]["hostPort"];
        //             unset($r["user_id"]);
        //             unset($r['pvc_name']);
        //             unset($r['pod_name']);
        //             unset($r['updated_at']);
        //             unset($r['id']);
        //         } 
        //     }
        // }
        // ddd($data);
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
    // function getK8sStatus(Request $request){
    //     // ddd(Auth::id());
    //     $userid = Auth::id();
    //     if($userid ==  null){
    //         return "error : no auth";
    //     }
    //     // $url = "http://192.168.11.6:8880/statusstream?userid=" . $userid;
    //     return new StreamedResponse(function()  {
    //         // $handle = fopen($url, 'r');
    //         // 外部APIからのSSEを受け取り、クライアントに送信する
    //         if (true) {
    //             while (true) {
    //                 // $data = fgets($handle);
    //                 $data = "12211";
    
    //                 // データをクライアントに送信
    //                 echo "data: {$data}\n\n";
   
    //                 // ブラウザにデータをフラッシュする
    //                 ob_flush();
    //                 flush();
    //                 sleep(1);
    //             }
    //             fclose($handle);
    //         }
    //     }, 200, [
    //         'Content-Type' => 'text/event-stream',
    //         'Cache-Control' => 'no-cache',
    //         'Connection' => 'keep-alive',
    //     ]);    
    // }

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
        $url = "http://192.168.11.6:8880/delete?podname=" . $podname . "&pvcname=" . $pvcname;
        $responce = Http::GET($url);
        $mc_server->where([['user_id','=',$userid],['pod_name','=',$podname]])->delete();
        ddd($responce);
    }
}

