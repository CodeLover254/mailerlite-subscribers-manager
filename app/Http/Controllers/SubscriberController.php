<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriberEditFormRequest;
use App\Http\Requests\SubscriberFormRequest;
use App\Models\Group;
use App\Services\MailerliteApiService;
use App\Services\SubscriberSortFilterService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubscriberController extends Controller
{
    /**
     * @var MailerliteApiService
     */
    private $apiService;
    /**
     * @var Group
     */
    private $group;

    /**
     * @var SubscriberSortFilterService
     */
    private $sortFilterService;

    /**
     * SubscriberController constructor.
     * @param MailerliteApiService $mailerliteApiService
     * @param SubscriberSortFilterService $subscriberSortFilterService
     */
    public function __construct(MailerliteApiService $mailerliteApiService, SubscriberSortFilterService $subscriberSortFilterService)
    {
        $this->apiService = $mailerliteApiService;
        $this->sortFilterService=$subscriberSortFilterService;
        $this->group = Group::first();
    }

    /**
     * @return View
     * loads the view that contains a datatable to display subscribers
     */
    public function index(): View
    {
        return view('subscribers');
    }

    /**
     * @return JsonResponse
     * returns a list of subscribers belonging to a group
     * A request may be received to search or order and also paginate results
     */
    public function getSubscribers(Request $request): JsonResponse
    {
        $draw = intval($request->get('draw'));
        $searchTerm = $request->get('search')['value'];
        $startIndex = intval($request->get('start'));
        $length = intval($request->get('length'));
        $orderColumn = intval($request->get('order')[0]['column']);
        $direction = $request->get('order')[0]['dir'];

        //get the total number of subscribers
        $subscribersCountResponse = $this->apiService->getSubscribersCount($this->group);
        $count = $subscribersCountResponse['data']->count;

        $response=['draw'=>$draw];
        $response['recordsTotal']=$count;
        $response['recordsFiltered']=$count;
        $response['data']=[];

        //if the count is 0 just return a record showing 0 records total. No need to proceed
        if($count==0)return response()->json($response);

        //get the subscribers
        $apiResponse = $this->apiService->getSubscribers($this->group,0,$count);
        if(!$apiResponse['status'])return response()->json($response);
        $response['recordsFiltered']=$count;
        $data=$apiResponse['data'];

        if($searchTerm!=null && $searchTerm!=''){
            //filter the data if there is something to search
            $filtered = $this->sortFilterService->filterData($searchTerm,$data);
            $response['recordsFiltered']=count($filtered);
            $data=$filtered;
        }

        //order the data after it has been fetched and paginate it as required
        if($direction!=null && $direction!=""){
            $this->sortFilterService->orderData($orderColumn,$data,$direction);
            $response['data']=$this->sortFilterService->pageArray($startIndex,$length,$data);;
        }

        return response()->json($response);
    }



    /**
     * @return View
     * loads the view that contains a form to add a subscriber
     */
    public function showAddSubscriberForm(): View
    {
        return view('create-subscriber');
    }


    /**
     * @param array $response
     * @return array
     * extracts the status and message from the response
     */
    private function extractResponseInfo(array $response): array
    {
        return ['status' => $response['status'], 'message' => $response['message']];
    }

    /**
     * @param SubscriberFormRequest $request
     * @return RedirectResponse
     * Receives validated request containing new subscriber information
     * An new subscriber is created via an API call and a message
     * is shown to the user depending on whether the request was successful or not
     */
    public function storeNewSubscriber(SubscriberFormRequest $request): RedirectResponse
    {
        $subscriber = [
            'email' => $request->email,
            'name' => $request->name,
            'fields' => ['country' => $request->country],
        ];
        $response = $this->apiService->addSubscriber($this->group, $subscriber);
        return redirect()->back()->with($this->extractResponseInfo($response));
    }

    /**
     * @param string $email
     * @return View|RedirectResponse
     * Gets the subscriber email from the url parameter and queries the API.
     * if the subscriber does not exist the user is redirected and an error message is show
     * If the subscriber exist then a form is loaded with their details to modify
     */
    public function showSubscriberEditForm(string $email)
    {
        $response = $this->apiService->getSubscriber($email);
        if (!$response['status'])
            return redirect()->route('subscribers')->with($this->extractResponseInfo($response));
        return view('update-subscriber', compact('response'));
    }

    /**
     * @param SubscriberEditFormRequest $request
     * @param string $email
     * @return RedirectResponse
     * Received a validated request containing subscriber details that are
     * used to update the subscriber via and API call
     */
    public function updateSubscriber(SubscriberEditFormRequest $request, string $email): RedirectResponse
    {
        $updateInfo = [
            'name' => $request->name,
            'fields' => ['country' => $request->country]
        ];
        $response = $this->apiService->updateSubscriber($updateInfo, $email);
        return redirect()->route('subscribers')->with($this->extractResponseInfo($response));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Receives a user email through the request and performs validation
     * If the validation fails a json response is sent back with the error
     * If the validation passes an API call is made to delete the subscriber
     */
    public function deleteSubscriber(Request $request): JsonResponse
    {
        try {
            $this->validate($request, ['email' => 'required|email']);
            $response = $this->apiService->deleteSubscriber($this->group, $request->email);
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->setStatusCode(422)->json(['error' => 'Email is required']);
        }
    }
}
