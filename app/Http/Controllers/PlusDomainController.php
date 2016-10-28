<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GoogleClientWrapper;
use \Exception;
use CurlAn;

class PlusDomainController extends Controller
{
    public function index()
    {
        // TODO: fetch google plus activity data
        
        return view('plusdomain.timeline', ['activities' => null]);
    }

    public function newPost(Request $request)
    {
        $access_token = session('access_token');
        $googleWrapper = new GoogleClientWrapper($access_token);
        $mediaId = "";
        $mediaType = "";
        if ($request->hasFile('media') && $request->file('media')->isValid()) // only upload when media exists
        {
            $result = $googleWrapper->attachMedia($request);
            $mediaId = $result['result']->id;
            $mediaType = 'video';
            if($result['type'] == 'image')
            {
                $mediaType = 'photo';
            }
        }
        
        $postResult = $googleWrapper->createActivity($request, $mediaId, $mediaType);

        return redirect('+/home');
    }
}