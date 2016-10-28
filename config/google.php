<?php

return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => env('GOOGLE_APPLICATION_NAME', ''),

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id'       => env('GOOGLE_CLIENT_ID', ''),
    'client_secret'   => env('GOOGLE_CLIENT_SECRET', ''),
    'redirect_uri'    => env('GOOGLE_REDIRECT', ''),
    'scopes'          => ['https://www.googleapis.com/auth/userinfo.profile',
                          'https://www.googleapis.com/auth/userinfo.email',
                          'https://www.googleapis.com/auth/plus.me',
                          'https://www.googleapis.com/auth/plus.circles.read',
                          'https://www.googleapis.com/auth/plus.circles.write',
                          'https://www.googleapis.com/auth/plus.stream.read',
                          'https://www.googleapis.com/auth/plus.stream.write',
                          'https://www.googleapis.com/auth/plus.media.upload',],
    'access_type'     => 'offline',
    'approval_prompt' => 'auto',

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => env('GOOGLE_DEVELOPER_KEY', ''),

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Set the credentials JSON's location to use assert credentials, otherwise
    | app engine or compute engine will be used.
    |
    */
    'service' => [
        /*
        | Enable service account auth or not.
        */
        'enable' => env('GOOGLE_SERVICE_ENABLED', false),

        /*
        | Path to service account json file
        */
        'file' => env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION', '')
    ],
];

/*
GOOGLE_CLIENT_ID=171449057608-r2mc988okfusbfl6p3tivhgntj22jd13.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=BJ9fWdnkbJ81dp9FZVVfUjJT
GOOGLE_REDIRECT=http://localhost:9250/iKnow/auth/google/callback
GOOGLE_DEVELOPER_KEY=AIzaSyBpAMe34zHFPE3zoxwuqloQDQUIH2n9cTk
GOOGLE_SERVICE_ENABLED=true
GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=client_secret_107052042187621678259.json

https://script.google.com/a/macros/gedu.demo.eikontechnology.com/d/1NngrDWOK39TY5-CHcIqItUWO2E6rg3Ien9TDvWnn3w7k18Dg73ZYrpp8/edit
*/