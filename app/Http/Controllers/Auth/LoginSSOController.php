<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\MasterSSO;
use Illuminate\Support\Facades\DB;

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
                    //$userInfo = DB::select('select * from bbuser where username = ?', $shortUsername);
                    $userInfo = DB::select('select * from bbuser where username = ?', array('AG161790'));
                    if(count($userInfo) > 0)
                    {
                        $userId = $userInfo[0]->userid;
                        $username = $userInfo[0]->username;
                        $kodeKantor = $userInfo[0]->kode_kantor;
                        $name = $userInfo[0]->nama_lengkap;
                        $nik = $userInfo[0]->nik;
                        $email = $userInfo[0]->email;
                        $lastLogin = date('Y-m-d');
                        $divisi = $userInfo[0]->divisi;
                        $jabatan = $userInfo[0]->jabatan;
                        $userType = $userInfo[0]->usergroupid;
                        $avatarLink = $userInfo[0]->avatar;
                        $avatarDateline = $userInfo[0]->avatar_dateline;
                    }
                    else
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

                        $response = CurlAn::jsonPost('http://es.bpjsketenagakerjaan.go.id:8049/wscom/svc.json',
                        array(
                            'chId' => 'Eiken Google',
                            'invoke' => 'getLoginUsrPass',
                            'username' => 'AG161790',
                            'password' => 'WELCOME1'));
                            
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
                                /*
                                $id = $resultWS2['return']->kodeUser;
                                $kode_kantor = $resultWS2['return']->kodeKantor;
                                $username = $resultWS2['return']->kodeUser;
                                $nama_user = $resultWS2['return']->namaUser;
                                $nik = $resultWS2['return']->npk;
                                $email = $resultWS2['return']->email;
                                $last_login = date('d-m-Y');
                                $divisi = $resultWS2['return']->roleJabatan;
                                $jabatan = $resultWS2['return']->jabatanPegawai;
                                */
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
                            return view('login', ['logindata' => $data, 'loginMessage' => 'Login Failed!']);
                        }
                    }
                    
                    return view('autosubmit', $samlResponse);
                }
                catch (Exception $e)
                {
                    throw $e;
                }
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