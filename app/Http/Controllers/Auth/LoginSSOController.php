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
            $domain = '@'.config('sso.primarydomain');
            $username = $request->input('username');
            $username = str_replace($domain, '', $username);
            $shortUsername = $username;
            $username = $username . $domain;
            $password = $request->input('password');

            //authentikasi menggunakan IMAP Google
            $mastersso = new MasterSSO();
            $authSSO = array('username' => $username, 'password' => $password);
            $resultSSOCheck = $mastersso->authenticationImap($authSSO);
            session(['LOGIN_VALIDATE_IMAP' => $resultSSOCheck]);

            if($resultSSOCheck)
            {
                $authenticationGoogle   = session('authenticationGoogle');
                $samlResponse = $mastersso->getSAMLResponse($authenticationGoogle['SAMLRequest'], $username);
                $samlResponse['RelayState'] = $authenticationGoogle['RelayState'];

                /*
                * Set BPJS login session
                */
                try
                {
                    // validate user name password
                    /*$response = CurlAn::jsonPost('http://es.bpjsketenagakerjaan.go.id:8049/wscom/svc.json',
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
                    }*/
                    
                    return view('autosubmit', $samlResponse);
                }
                catch (Exception $e)
                {
                    // TODO : handle something
                    throw $e;
                }

                // TODO: move this to page which called by redirect script
                // -------------------------------------------------------
                /*$authUrl = "auth/google";
                $isOauthValid = session("OAUTH_VALID");
                $isOauthValid = false;
                session(["OAUTH_VALID" => $isOauthValid]);
                return redirect($authUrl);*/
                // -------------------------------------------------------
            }

            $data = $this->_authcheck($request);
            if($data instanceof \Illuminate\Http\RedirectResponse)
            {
                return $data;
            }
            return view('login', ['logindata' => $data, 'loginMessage' => 'Login Failed!']);
        }
        
        $data = $this->_authcheck($request);
        if($data instanceof \Illuminate\Http\RedirectResponse)
        {
            return $data;
        }
        return view('login', ['logindata' => $data, 'loginMessage' => '']);
    }
    
    /**
    * Check authentication.
    *
    * @param Request $request web request.
    */
    protected function _authcheck(Request $request)
    {
        $domain = config('sso.primarydomain');
        //$url_location           = "https://mail.google.com/a/" . $domain; // TODO: change into script redirect
        $url_location = "https://script.google.com/a/macros/gedu.demo.eikontechnology.com/s/AKfycbzV0Lp2NJwG_7flcAQWJUDwo94NanIqI4Vm4iOO-f0EHsaUUO4/exec";
        $authenticationGoogle   = session('authenticationGoogle');
        
        if(empty($authenticationGoogle))
        {
            //print_r($_GET);exit;
            if(empty($request->input('SAMLRequest')) || empty($request->input('RelayState')))
            {
                return redirect($url_location);
            }
            else
            {
                if(empty($authenticationGoogle))
                {
                    $samlRelay = array(
                        'SAMLRequest' => $request->input('SAMLRequest'),
                        'RelayState'  => $request->input('RelayState'));

                    session(['authenticationGoogle' => $samlRelay]);
                    
                    return redirect('login');
                }
                else
                {
                    return $authenticationGoogle;
                }
            }
        }

        return array('SAMLRequest' => $authenticationGoogle['SAMLRequest'],
                     'RelayState' => $authenticationGoogle['RelayState']);
    }
}