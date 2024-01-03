<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\McServer;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\GsCreateRequest;

class DashboardController extends Controller
{
    function index () {
        $userid = Auth::id();
        return view('dashboard');
    }
    function create() {
        $user = Auth::id();
        return view('create');
    }
    function createServer(GsCreateRequest $request){
        $server_name = $request["server_name"];
        $domain = $request["domain"];
        $userid = Auth::id();

        $mc_server  = new McServer;

        $response = Http::get("http://192.168.11.6:8880/create?userid=" . $userid . "&sname=" . $server_name);
        $data = $response->json();
        $mc_server->name = $server_name;
        $mc_server->pod_name = $data['createdGS']["metadata"]["name"];
        $mc_server->user_id = $userid;
        $mc_server->domain = $domain;
        $mc_server->pvc_name = $data['createdPVC']['metadata']['name'];
        $mc_server->save();
        return redirect()->route('dashboard');
    }
}
