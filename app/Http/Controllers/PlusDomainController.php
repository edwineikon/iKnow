<?php

namespace App\Http\Controllers;

use \Exception;
use Illuminate\Http\Request;
use App\Helpers\GoogleClientWrapper;
use CurlAn;

class PlusDomainController extends Controller
{
    public function index()
    {
        return view('plusdomain.timeline');
    }

    public function getActivities($page=0, $pageSize=10)
    {
        $access_token = session('access_token');
        $googleWrapper = new GoogleClientWrapper($access_token);
        return $googleWrapper->getActivities($page, $pageSize);
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

    public function newCircle(Request $request)
    {
        $access_token = session('access_token');
        $googleWrapper = new GoogleClientWrapper($access_token);
    }
}