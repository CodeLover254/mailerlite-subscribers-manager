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

    /**
     * @return string
     * gets the api key from the database
     */
    private function getStoredApiKey():string
    {
        return ApiUser::first()->api_key;
    }

    /**
     * @param string|null $apiKey
     * @return PendingRequest
     * creates a http client with the required header for api authorization
     */
    public function buildHTTPClient(string $apiKey=null): PendingRequest
    {
        if($apiKey==null)$apiKey=$this->getStoredApiKey();
        return Http::withHeaders(['X-MailerLite-ApiKey'=>$apiKey]);
    }

    /**
     * @param string $apiKey
     * @return bool
     * checks if an api key is valid by simply attempting to get subscribers
     * If the api key is valid the http status code will be 200
     */
    public function validateApiKey(string $apiKey): bool
    {
        $response = $this->buildHTTPClient($apiKey)->get(self::SUBSCRIBER_ENDPOINT_URL);
        return $response->status()==self::HTTP_STATUS_OK;
    }

    /**
     * @param array $groupInfo
     * @return array
     * Creates a group using provided group information
     */
    public function createGroup(array $groupInfo): array
    {
        $response = $this->buildHTTPClient()->post(self::GROUP_ENDPOINT_URL,$groupInfo);
        return $this->handleResponse($response);
    }

    /**
     * @param Group $group
     * @param int $offset
     * @param int $limit
     * @return array
     * Queries the API for subscribers of a given group
     */
    public function getSubscribers(Group $group, int $offset=0, int $limit=100): array
    {
        //for simplicity we get only subscribers belonging to the group we just created
        $response = $this->buildHTTPClient()->get(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers',
            ['offset'=>$offset,'limit'=>$limit]);
        return $this->handleResponse($response);
    }

    /**
     * @param Group $group
     * @return array
     * Queries the API and returns the total number of subscribers in a group
     */
    public function getSubscribersCount(Group $group):array
    {
        $response = $this->buildHTTPClient()->get(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers/count');
        return $this->handleResponse($response);
    }

    /**
     * @param string $email
     * @return array
     * Queries the API for a single subscriber given their email
     */
    public function getSubscriber(string $email): array
    {
        $response = $this->buildHTTPClient()->get(self::SUBSCRIBER_ENDPOINT_URL.'/'.$email);
        return $this->handleResponse($response);
    }

    /**
     * @param Group $group
     * @param array $subscriber
     * @return array
     * Adds a subscriber to a group
     */
    public function addSubscriber(Group $group, array $subscriber): array
    {
        $response = $this->buildHTTPClient()
                         ->post(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers', $subscriber);
        return $this->handleResponse($response);
    }

    /**
     * @param array $subscriber
     * @param string $subscriberEmail
     * @return array
     * Updates a given subscriber
     */
    public function updateSubscriber(array $subscriber, string $subscriberEmail): array
    {
        $response = $this->buildHTTPClient()->put(self::SUBSCRIBER_ENDPOINT_URL.'/'.$subscriberEmail,$subscriber);
        return $this->handleResponse($response);
    }

    /**
     * @param Group $group
     * @param string $email
     * @return array
     * Deletes a subscriber
     */
    public function deleteSubscriber(Group $group, string $email): array
    {
        $response = $this->buildHTTPClient()
                         ->delete(self::GROUP_ENDPOINT_URL.'/'.$group->group_id.'/subscribers/'.$email);
        return $this->handleResponse($response);
    }

    /**
     * @param Group $group
     * @return array
     * Deletes a group
     */
    public function deleteGroup(Group $group): array
    {
        $response = $this->buildHTTPClient()->delete(self::GROUP_ENDPOINT_URL.'/'.$group->group_id);
        return $this->handleResponse($response);
    }

    /**
     * @param Response $response
     * @return array
     * Processes the response object and returns an array containing information
     * that can be used in views
     */
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
