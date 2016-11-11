<?php

namespace App\Helpers;

use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PulkitJalan\Google\Client;
use App\ViewModels;
use Socialite;

class GoogleClientWrapper {

    protected $client;
    protected $service;

    function __construct($accessToken = '') {
        $config = config('google');
        $this->client = new Client($config);
        $this->client->authorize();
        $this->client->setAccessToken($accessToken);
        $this->service = $this->client->make('plusDomains');
    }
    
    public function getActivities($page = 0, $pageSize = 10)
    {
        try
        {
            //$this->service = $this->client->make('plusDomains');
            $user = Socialite::driver('google')->user();
            $userId = $user->id;

            $result = array();
            $recordCount = DB::select('SELECT count(*) AS TotalRecord FROM gplusd_activity WHERE flag=1')[0]->TotalRecord;
            $totalPage = 0;
            if($recordCount > 0)
            {
                $totalPage = ceil(($recordCount/$pageSize));
            }

            $activities = DB::select('SELECT activityid, userid, flag FROM gplusd_activity WHERE flag=1 LIMIT ?,?', array($page, $pageSize));
            foreach ($activities as $act)
            {
                $currentAct = $this->getActivity($act->activityid);
                if ($act->userid == $userId)
                {
                    $result[] = $currentAct;
                }
                else
                {
                    $listPeopleResult = $this->service->people->listByActivity($act->activityid, 'sharedto');

                    foreach ($listPeopleResult->items as $people)
                    {
                        if($people->userId == $userId)
                        {
                            $result[] = $currentAct;
                            break;
                        }
                    }

                    if(!empty($listPeopleResult->nextPageToken))
                    {
                        $this->recursiveListPeopleByActivity($act->activityid, $userId, $result, $listPeopleResult->nextPageToken);
                    }
                }
            }

            /*$this->service = $this->client->make('plusDomains');
            $optParams = array('maxResults' => 100);
            $activities = $this->service->activities->listActivities('me', 'user', $optParams);*/

            return array('data'=>$result, 'totalRows'=>$recordCount, 'totalPage'=> $totalPage);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    protected function recursiveListPeopleByActivity($activityId, $userId, $result, $pageToken = "")
    {
        //$this->service = $this->client->make('plusDomains');
        $currentAct = $this->getActivity($activityId);
        $listPeopleResult = $this->service->people->listByActivity($activityId, 'sharedto', array('pageToken' => $pageToken));
        foreach ($listPeopleResult->items as $people)
        {
            if($people->userId == $userId)
            {
                $result[] = $currentAct;
                break;
            }

            if(!empty($listPeopleResult->nextPageToken))
            {
                $this->recursiveListPeopleByActivity($activityId, $userId, $result, $listPeopleResult->nextPageToken);
            }
        }
    }

    public function getActivity($activityId)
    {
        //$this->service = $this->client->make('plusDomains');
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

                //$this->service = $this->client->make('plusDomains');
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

            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->activities->insert('me', $activity);

            $user = Socialite::driver('google')->user();
            $activityId = $result->id;
            $userId = $user->id;
            DB::insert("INSERT INTO gplusd_activity (activityid, userid) VALUES (?, ?)", array($activityId, $userId));

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public function createCircle(Request $request)
    {
        try
        {
            $circle = new \Google_Service_PlusDomains_Circle();
            $circle->setDisplayName($request->input('txtDisplayName'));
            
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->insert('me', $circle);

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public function updateCircle(Request $request)
    {
        try
        {
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->update($request->input('circleId'),
                array("description" => $request->input('txtDescription')));

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public function removeCircle($circleId)
    {
        try
        {
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->remove($circleId);

            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function addPeopleToCircle(Request $request)
    {
        try
        {
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->addPeople($request->input('circleId'),
                array("email" => $request->input('txtEmail'),
                       "userId" => $request->input('optUser')));

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public function removePeopleFromCircle($circleId, $userId, $email)
    {
        try
        {
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->removePeople($circleId,
                array("email" => $userId,
                       "userId" => $email));

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}