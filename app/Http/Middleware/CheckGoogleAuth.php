<?php

namespace App\Http\Middleware;

use Closure;
use PulkitJalan\Google\Client;
use \Exception;
use Socialite;

class CheckGoogleAuth
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ssoUrl = "login";
        $authUrl = "auth/google";

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
            $input = trim($request->input('data'));
            if(empty($input))
            {
                if ($request->path() != $ssoUrl)
                {
                    return redirect($ssoUrl);
                }
            }
        }
        else
        {
            if ($isSSOAuthenticated && !$isOauthValid)
            {
                if ($request->path() != $authUrl)
                {
                    return redirect($authUrl);
                }
            }

            try
            {
                $access_token = session('access_token');
                if(!empty($access_token))
                {
                    $config = config('google');
                    $client = new Client($config);
                    $client->authorize();
                    $client->setAccessToken($access_token);
                    $service_plus = $client->make('plusDomains');
                    $service_plus->people->get('me');
                }
                else
                {
                    $user = Socialite::driver('google')->user();
                    session(["access_token" => $user->token]);
                    $access_token = session('access_token');

                    $config = config('google');
                    $client = new Client($config);
                    $client->authorize();
                    $client->setAccessToken($access_token);
                    $service_plus = $client->make('plusDomains');
                    $service_plus->people->get('me');
                }
            }
            catch (Exception $e)
            {
                if ($request->path() != $authUrl)
                {
                    return redirect($authUrl);
                }
            }
        }
        
        return $next($request);
    }
}