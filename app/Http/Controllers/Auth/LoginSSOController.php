<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\MasterSSO;

class LoginSSOController extends Controller
{
    public function login(Request $request)
    {
        $method = $request->method();
        if ($request->isMethod('post'))
        {
            $domain = $request->input('domain');
            $username = $request->input('username');
            $username = str_replace($domain, '', $username);
            $shortUsername = $username;
            $username = $username . $domain;
            $password = $request->input('password');

            //authentikasi menggunakan IMAP Google
            $mastersso = new MasterSSO();
            $authSSO = array('username' => $username, 'password' => $password);
            $resultSSOCheck = $mastersso->authenticationImap($authSSO);

            if($resultSSOCheck)
            {
                $isSSOAuthenticated = true;
                session(["SSO_AUTH" => $isSSOAuthenticated]);

                /*
                * Set BPJS login session
                */
                try
                {
                    // validate user name password
                    $response = CurlAn::jsonPost('http://es.bpjsketenagakerjaan.go.id:8049/wscom/svc.json',
                    array(
                        'chId' => 'Eiken Google',
                        'invoke' => 'getLoginUsrPass',
                        'username' => $shortUsername,
                        'password' => $password ));
                        
                    $result = explode(',',str_replace('}','',str_replace('{','',$response->body)));
                    $validateResult = str_replace('"', '', explode(':', $result[3])[1]);
                    if($validateResult == "Sukses")
                    {
                        $response = CurlAn::jsonPost('http://es.bpjsketenagakerjaan.go.id:8049/wscom/svc.json',
                        array(
                            'chId' => 'Eiken Google',
                            'invoke' => 'getUserLoginInfo',
                            'kodeUser' => $shortUsername));
                        $result = explode(',',str_replace('}','',str_replace('{','',$response->body)));
                        $validateResult = str_replace('"', '', explode(':', $result[3])[1]);
                        if($validateResult == "Sukses")
                        {
                            // TODO : set session info
                        }
                        else
                        {
                            // TODO : what to do when sso succeed, but return error from BPJS api
                        }
                    }
                    else
                    {
                        // TODO : what to do when sso succeed, but return error from BPJS api
                    }
                }
                catch (Exception $e)
                {
                    // TODO : handle something
                    throw $e;
                }

                $authUrl = "auth/google";
                $isOauthValid = session("OAUTH_VALID");
                $isOauthValid = false;
                session(["OAUTH_VALID" => $isOauthValid]);

                return redirect($authUrl);
            }
            else
            {
                $data = $this->_authcheck($request);
                return view('login', ['logindata' => $data, 'loginMessage' => 'Login Failed!']);
            }
        }
        else
        {
            $data = $this->_authcheck($request);
            return view('login', ['logindata' => $data]);
        }
    }
    
    /**
    * Check authentication.
    *
    * @param Request $request web request.
    */
    protected function _authcheck(Request $request)
    {
        $domain = config('sso.primarydomain');
        $url_location           = "https://mail.google.com/a/" . $domain;
        $SAMLRequest            = $request->input('SAMLRequest');
        $authenticationGoogle   = session('authenticationGoogle');

        if(empty($authenticationGoogle))
        {
             if(empty($request->input('SAMLRequest')) || empty($request->input('RelayState')))
             {
                return redirect($url_location);
             }
             else
             {
                $samlRelay = array(
                    'SAMLRequest' => $request->input('SAMLRequest'),
                    'RelayState'  => $request->input('RelayState'));

                session(['authenticationGoogle' => $samlRelay]);
                return $samlRelay;
             }
        }

        return array('SAMLRequest' => $authenticationGoogle['SAMLRequest'],
                     'RelayState' => $authenticationGoogle['RelayState']);
    }
}