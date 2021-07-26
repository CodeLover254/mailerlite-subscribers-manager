<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\MailerliteApiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * @var MailerliteApiService
     */
    private $apiService;

    /**
     * GroupController constructor.
     * @param MailerliteApiService $mailerliteApiService
     */
    public function __construct(MailerliteApiService $mailerliteApiService)
    {
        $this->apiService = $mailerliteApiService;
    }

    /**
     * @return View
     * loads the view with a form to create a new group
     */
    public function showAddGroupForm(): View
    {
        return view('create-group');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * Receives group name, validates it and creates a group via
     * an API call. If the request is successful the group is saved
     * in the database and the user redirected to the subscriber creation form.
     */
    public function storeNewGroup(Request $request): RedirectResponse
    {
        $this->validate($request, ['group_name' => 'required|max:50']);
        $response = $this->apiService->createGroup(['name' => $request->group_name]);
        if (!$response['status']) {
            return redirect()->back()->with($response);
        }
        Group::create(['group_id' => $response['data']->id, 'group_name' => $response['data']->name]);
        return redirect()->route('show-add-subscriber')->with($response);
    }
}
