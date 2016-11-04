<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\MasterSSO;
use Illuminate\Support\Facades\DB;
use CurlAn;

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
                session(['loggedinusername' => $shortUsername]);

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

                        session(['loggedinfullname' => $name]);
                        /*$avatarLink = $userInfo[0]->avatar;
                        $avatarDateline = $userInfo[0]->avatar_dateline;*/

                        // TODO: call ws to set session
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
                                // Get user info
                                $id = str_replace('"', '', explode(':', $result[8])[1]);
                                $kode_kantor = str_replace('"', '', explode(':', $result[7])[1]);
                                $username = $id;
                                $nama_user = str_replace('"', '', explode(':', $result[10])[1]);
                                $nik = str_replace('"', '', explode(':', $result[11])[1]);
                                $email = str_replace('"', '', explode(':', $result[5])[1]);
                                $last_login = date('d-m-Y');
                                $divisi = str_replace('"', '', explode(':', $result[12])[1]);
                                $jabatan = str_replace('"', '', explode(':', $result[6])[1]);

                                $response = CurlAn::jsonPost('http://es.bpjsketenagakerjaan.go.id:8049/wscom/svc.json',
                                array(
                                    'chId' => 'Eiken Google',
                                    'invoke' => 'getListUserFungsi',
                                    'kodeUser' => $id,
                                    'kodeKantor' => 0));
                                $result = explode(',',str_replace('}','',str_replace('{','',$response->body)));
                                $validateResult = str_replace('"', '', explode(':', $result[3])[1]);
                                if($validateResult == "Sukses")
                                {
                                    if(str_replace('"', '', explode(':', $result[5])[0]) == "userFungsiObj")
                                    {
                                        $fungsi = explode(' - ', str_replace('"', '', explode(':', $result[8])[1]));
                                        $divisi = $fungsi[1];
                                        $jabatan = $fungsi[0];
                                    }
                                    else
                                    {
                                        $fungsi = explode(' - ', str_replace('"', '', explode(':', $result[7])[1]));
                                        $divisi = $fungsi[1];
                                        $jabatan = $fungsi[0];
                                    }
                                }

                                // Insert into bbuser
                                $token = '$2y$10$ruD3EqfvR8Cxh4VHU48yiuvFUp8icmkPQVX4HVdAxh1TNS3seLerm';
                                $secret = "l,zPm>\'&C.xKKfU;B-aqb>?l,%B%1";
                                $sql_insert = "INSERT INTO `bbuser`(usergroupid,username,token,scheme,secret,new_password,passworddate,email,usertitle,
                                               divisi,jabatan,kode_kantor,nama_lengkap,nik,showvbcode,joindate,lastvisit,lastactivity,ipaddress)
                                               VALUES(2,'{$username}','{$token}','blowfish:10','{$secret}','".md5($password)."','".date('Y-m-d').
                                               "','{$email}','Newbie','{$divisi}','{$jabatan}','{$kode_kantor}','{$nama_user}','{$nik}',1,
                                               {$last_login},{$last_login},{$last_login},'".Request::ip()."')";
                                DB::insert($sql_insert);
                                session(['loggedinfullname' => $nama_user]);

                                // TODO: call ws to set session
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
        $authenticationGoogle = session('authenticationGoogle');
        
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