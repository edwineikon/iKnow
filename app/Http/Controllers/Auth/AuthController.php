<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use \Exception;
use PulkitJalan\Google\Client;

class AuthController extends Controller
{
    protected $redirectTo = '/';
    protected $logoutUrl = 'https://accounts.google.com/Logout';

    public function logout()
    {
        $isSSOAuthenticated = false;
        session(["SSO_AUTH" => $isSSOAuthenticated]);

        $isLoginValidateIMAP = false;
        session(['LOGIN_VALIDATE_IMAP' => $isLoginValidateIMAP]);

        $isOauthValid = false;
        session(["OAUTH_VALID" => $isOauthValid]);
        session(["access_token" => ""]);
        session()->flush();

        return redirect($this->logoutUrl);
    }

    public function redirectToGoogle()
    {
        $isSSOAuthenticated = session("SSO_AUTH");
        $isOauthValid = session("OAUTH_VALID");
        $isLoginValidateIMAP = session("LOGIN_VALIDATE_IMAP");

        if(empty($isSSOAuthenticated))
        {
            $isSSOAuthenticated = false;
            session(["SSO_AUTH" => $isSSOAuthenticated]);
        }
        
        if(empty($isOauthValid))
        {
            $isOauthValid = false;
            session(["OAUTH_VALID" => $isOauthValid]);
        }

        if(empty($isLoginValidateIMAP))
        {
            $isLoginValidateIMAP = false;
            session(['LOGIN_VALIDATE_IMAP' => $isLoginValidateIMAP]);
        }
        
        if(!$isSSOAuthenticated && !$isLoginValidateIMAP)
        {
            return redirect("login");
        }
        else
        {
            if ($isLoginValidateIMAP)
            {
                $isSSOAuthenticated = true;
                session(["SSO_AUTH" => $isSSOAuthenticated]);
            }

            if(!$isOauthValid)
            {
                return Socialite::with('google')->scopes([
                    'https://www.googleapis.com/auth/userinfo.profile',
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/plus.me',
                    'https://www.googleapis.com/auth/plus.circles.read',
                    'https://www.googleapis.com/auth/plus.circles.write',
                    'https://www.googleapis.com/auth/plus.stream.read',
                    'https://www.googleapis.com/auth/plus.stream.write',
                    'https://www.googleapis.com/auth/plus.media.upload',])->redirect();
            }
            else
            {
                try
                {
                    $user = Socialite::driver('google')->user();
                    
                    return redirect($this->redirectTo);
                }
                catch (Exception $e)
                {
                    return redirect('auth/google');
                }
            }
        }
    }

    public function handleGoogleCallback()
    {
        try
        {
            $user = Socialite::driver('google')->user();
            session(["access_token" => $user->token]);
            $isOauthValid = true;
            session(["OAUTH_VALID" => $isOauthValid]);

            return redirect($this->redirectTo);
        }
        catch (Exception $e)
        {
            $isSSOAuthenticated = false;
            session(["SSO_AUTH" => $isSSOAuthenticated]);

            $isLoginValidateIMAP = false;
            session(['LOGIN_VALIDATE_IMAP' => $isLoginValidateIMAP]);

            $isOauthValid = false;
            session(["OAUTH_VALID" => $isOauthValid]);
            session(["access_token" => ""]);
            session()->flush();

            return redirect($this->logoutUrl);
        }
    }
}