<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use PulkitJalan\Google\Client;
use \Exception;
use App\ViewModels;

class GoogleClientWrapper {

    protected $client;
    protected $service;

    function __construct($accessToken = '') {
        $config = config('google');
        $this->client = new Client($config);
        $this->client->authorize();
        $this->client->setAccessToken($accessToken);    
    }
    
    public function getActivities()
    {
        $this->service = $this->client->make('plusDomains');
        $optParams = array('maxResults' => 100);
        $activities = $this->service->activities->listActivities('me', 'user', $optParams);

        return $activities;
    }

    public function getActivity($activityId)
    {
        $this->service = $this->client->make('plusDomains');
        $result = $this->service->activities->get($activityId);
        
        return $result;
    }

    public function attachMedia(Request $request)
    {
        try
        {
            if ($request->hasFile('media') && $request->file('media')->isValid())
            {
                $type = 'video';
                $extension = $request->media->extension();
                if ($extension == "jpg" || $extension == "png")
                {
                    $type = 'image';
                }
                
                $file_path = $request->media->path();
                $file_data = file_get_contents($file_path);
                $file_title = basename($file_path);
                
                $file = new \Google_Service_PlusDomains_Media();
                $file->setDisplayName($request->input('txtTitle'));

                $this->service = $this->client->make('plusDomains');
                $result = $this->service->media->insert('me','cloud', $file,
                    array(
                        'data' => $file_data,
                        'mimeType' => $type . '/*',
                        'uploadType' => 'media',
                ));

                return array('result' => $result, 'type' => $type);
            }

            return null;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public function createActivity(Request $request, $mediaId = "", $mediaType = "")
    {
        try
        {
            $items = new \Google_Service_PlusDomains_PlusDomainsAclentryResource();
            $items->setType('domain');

            $acl = new \Google_Service_PlusDomains_Acl();
            $acl->setDomainRestricted(true);
            $acl->setItems(array(
                $items
            ));

            $actObject = new \Google_Service_PlusDomains_ActivityObject();
            $actObject->setOriginalContent($request->input('txtBody'));

            if(!empty($mediaId))
            {
                $actObject->setContent($request->input('txtBody'));
                $attachments = new \Google_Service_PlusDomains_ActivityObjectAttachments();
                $attachments->setId($mediaId);
                $attachments->setObjectType($mediaType);
                $actObject->setAttachments(array(
                    $attachments
                ));
            }

            $activity = new \Google_Service_PlusDomains_Activity();
            $activity->setTitle($request->input('txtTitle'));
            $activity->setAccess($acl);
            $activity->setVerb('post');
            $activity->setObject($actObject);

            $this->service = $this->client->make('plusDomains');
            $result = $this->service->activities->insert('me', $activity);

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}