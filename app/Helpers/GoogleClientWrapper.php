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

    public function getCurrentUserId()
    {
        try
        {
            $user = $this->service->people->get('me');
            return $user->id;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
    
    public function getActivities($page = 0, $pageSize = 10)
    {
        try
        {
            $userId = $this->getCurrentUserId();

            $result = array();
            $recordCount = DB::select('SELECT COUNT(*) AS TotalRecord FROM (
                                        SELECT a.id, a.userid, a.activityid, a.flag
                                        FROM gplus_activity a INNER JOIN gplus_user b ON a.userid = b.id
                                        WHERE b.userid = ? AND a.flag = 1
                                        UNION
                                        SELECT id, userid, activityid, flag
                                        FROM gplus_activity
                                        WHERE userid IN
                                        (
                                            SELECT c.userid
                                            FROM gplus_activity_circle a
                                            INNER JOIN gplus_circle b ON a.circleid = b.id
                                            INNER JOIN gplus_circle_detail c ON b.id = c.circleid
                                            WHERE a.activityid = activityid
                                        ) AND flag = 1
                                    ) ut')[0]->TotalRecord;
            $totalPage = 0;
            if($recordCount > 0)
            {
                $totalPage = ceil(($recordCount/$pageSize));
            }

            $activities = DB::select('SELECT id, userid, activityid, flag FROM (
                                        SELECT a.id, a.userid, a.activityid, a.flag
                                        FROM gplus_activity a INNER JOIN gplus_user b ON a.userid = b.id
                                        WHERE b.userid = ? AND a.flag = 1
                                        UNION
                                        SELECT id, userid, activityid, flag
                                        FROM gplus_activity
                                        WHERE userid IN
                                        (
                                            SELECT c.userid
                                            FROM gplus_activity_circle a
                                            INNER JOIN gplus_circle b ON a.circleid = b.id
                                            INNER JOIN gplus_circle_detail c ON b.id = c.circleid
                                            WHERE a.activityid = activityid
                                        ) AND flag = 1
                                    ) ut LIMIT ?,?;', array($userId, $page, $pageSize));
            foreach ($activities as $act)
            {
                $currentAct = $this->getActivity($act->activityid);
                $result[] = $currentAct;
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
            
            $activityId = $result->id;
            $userId = $this->getCurrentUserId();
            $dbUserId = DB::select("SELECT id AS DbId FROM gplus_user WHERE userid=?", array($userId))[0]->DbId;
            $lastActId = DB::table('gplus_activity')->insertGetId(
                ['activityid' => $activityId, userid => $dbUserId]
            );

            $activityCircles = array();

            /*DB::table('gplus_activity_circle')->insert([
                ['email' => 'taylor@example.com', 'votes' => 0],
                ['email' => 'dayle@example.com', 'votes' => 0]
            ]);*/
            DB::table('gplus_activity_circle')->insert($activityCircles);

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public function retrieveCirclesForUser($userid)
    {
        $result = array();

        $user = DB::table('gplus_user')->where('userid', $userid)->first();
        if($user)
        {
            $dbUserId = $user->id;

            $circles = DB::table('gplus_circle')->where('userid', $dbUserId);
            foreach ($circles as $circle)
            {
                $currentCircle = getCircle($circle->circleid);
                $result[] = $currentCircle;
            }
        }

        return $result;
    }

    public function getCircle($circleId)
    {
        $result = $this->service->circles->get($circleId);
        return $result;
    }

    public function createCircle(Request $request)
    {
        try
        {
            $circle = new \Google_Service_PlusDomains_Circle();
            $circle->setDisplayName($request->input('txtDisplayName'));
            
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->insert('me', $circle);
            
            $userId = $this->getCurrentUserId();
            $dbUserId = DB::select("SELECT id AS DbId FROM gplus_user WHERE userid=?", array($userId))[0]->DbId;
            $circleId = DB::table('gplus_circle')->insertGetId([
                ['circleid' => $result->id, 'userid' => $dbUserId]
            ]);

            return $circleId;
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
            DB::table('gplus_circle')->where('circleid', $circleId)->delete();

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
            $realCircleId = DB::table('gplus_circle')->where('id', $request->input('circleId'))->first()->circleid;
            //$this->service = $this->client->make('plusDomains');
            $result = $this->service->circles->addPeople($realCircleId,
                array("email" => $request->input('txtEmail'),
                      "userId" => $request->input('optUser')));
            
            $userId = DB::table('gplus_user')->where('userid', $request->input('optUser'))->first()->id;
            DB::table('gplus_activity_circle')->insert([
                ['userid' => $userId, 'circleId' => $request->input('circleId')]
            ]);

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
            $dbCircleId = DB::table('gplus_circle')->where('circleid', $circleId)->first()->id;
            $dbUserId = DB::table('gplus_user')->where('userid', $userId)->first()->id;
            DB::table('gplus_circle_detail')->where([
                ['circleid', '=', $dbCircleId],
                ['userid', '=', $dbUserId]
            ])->delete();

            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}