<?php

namespace App\Http\Controllers;

use Bbt\Sso\BbtSsoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AuthController extends _BaseController
{
    // private BbtSsoClient $ssoClient;
    
    function __construct()
    {
        parent::__construct();
        
        $sso_conf = Config::get("sso");
        // $this->ssoClient = new BbtSsoClient(
        //     $sso_conf['url'], 
        //     $sso_conf['client_id'], 
        //     $sso_conf['client_secret'], 
        //     $sso_conf['url_local']
        // );
    }

    public function sso_callback(Request $request){
        // $response = $this->ssoClient->SsoCallbackHandler();

        // request()->session()->put('user', $response);
        // // Session::put('user', json_decode(json_encode($response), true));
        // // Session::put('user', $response);

        // $redirect = request()->get('redirect', null);
		// if(!empty($redirect)){
		// 	return redirect($redirect);
		// }

        // return redirect()->route('home');
    }

    public function sso_logout(Request $request)
    {
        // $request->session()->invalidate();
        // // $request->session()->regenerateToken();

        // $this->ssoClient->Logout();
    }

    public function temp(Request $request){
        return view('pages/temp');
    }
}
