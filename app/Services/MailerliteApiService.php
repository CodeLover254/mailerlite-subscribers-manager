<?php


namespace App\Services;


use App\Models\ApiUser;
use App\Models\Group;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


class MailerliteApiService
{
    const SUBSCRIBER_ENDPOINT_URL='https://api.mailerlite.com/api/v2/subscribers';
    const GROUP_ENDPOINT_URL='https://api.mailerlite.com/api/v2/groups';
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_CREATED = 201;
    const HTTP_STATUS_NO_CONTENT = 204;
    const HTTP_STATUS_BAD_REQUEST= 400;
    const HTTP_STATUS_UNAUTHORISED = 401;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_ERROR = 500;

    private function getStoredApiKey():string
    {
        return ApiUser::first()->api_key;
    }

    public function buildHTTPClient(string $apiKey=null): PendingRequest
    {
        if($apiKey==null)$apiKey=$this->getStoredApiKey();
        return Http::withHeaders(['X-MailerLite-ApiKey'=>$apiKey]);
    }

    public function validateApiKey(string $apiKey): bool
    {
        $response = $this->buildHTTPClient($apiKey)->get(self::SUBSCRIBER_ENDPOINT_URL);
        return $response->status()==self::HTTP_STATUS_OK;
    }

    public function createGroup(array $groupInfo): array
    {
        $response = $this->buildHTTPClient()->post(self::GROUP_ENDPOINT_URL,$groupInfo);
        return $this->handleResponse($response);
    }

    public function getSubscribers(Group $group, int $offset=0,int $limit=100): array
    {
        //for simplicity we get only subscribers belonging to the group we just created
        $response = $this->buildHTTPClient()->get(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers',
            ['offset'=>$offset,'limit'=>$limit]);
        return $this->handleResponse($response);
    }

    public function getSubscriber(string $email): array
    {
        $response = $this->buildHTTPClient()->get(self::SUBSCRIBER_ENDPOINT_URL.'/'.$email);
        return $this->handleResponse($response);
    }

    public function addSubscriber(Group $group, array $subscriber): array
    {
        $response = $this->buildHTTPClient()
                         ->post(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers', $subscriber);
        return $this->handleResponse($response);
    }

    public function updateSubscriber(array $subscriber, string $subscriberEmail): array
    {
        $response = $this->buildHTTPClient()->put(self::SUBSCRIBER_ENDPOINT_URL.'/'.$subscriberEmail,$subscriber);
        return $this->handleResponse($response);
    }

    public function deleteSubscriber(Group $group, string $email): array
    {
        $response = $this->buildHTTPClient()
                         ->delete(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers/'.$email);
        return $this->handleResponse($response);
    }

    public function deleteGroup(Group $group): array
    {
        $response = $this->buildHTTPClient()->delete(self::GROUP_ENDPOINT_URL.'/'.$group->group_id);
        return $this->handleResponse($response);
    }

    private function handleResponse(Response $response): array
    {
        $results=['status'=>false,'message'=>null,'data'=>null];
        $jsonResponse = json_decode($response->body());
        switch($response->status())
        {
            case self::HTTP_STATUS_OK:
            case self::HTTP_STATUS_CREATED:
                $results['status']=true;
                $results['message']='Operation Successful';
                $results['data']=$jsonResponse;
                break;
            case self::HTTP_STATUS_NO_CONTENT:
                $results['status']=true;
                $results['message']='Operation Successful';
                break;
            case self::HTTP_STATUS_NOT_FOUND:
            case self::HTTP_STATUS_ERROR:
            case self::HTTP_STATUS_BAD_REQUEST:
            case self::HTTP_STATUS_UNAUTHORISED:
                $results['message']=$jsonResponse->error->message;
                break;
        }

        return $results;
    }
}
