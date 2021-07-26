<?php

namespace App\Http\Controllers;

use App\Models\ApiUser;
use App\Services\MailerliteApiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    /**
     * @var MailerliteApiService
     */
    private $apiService;

    /**
     * ApiUserController constructor.
     * @param MailerliteApiService $mailerliteApiService
     * constructor
     */
    public function __construct(MailerliteApiService $mailerliteApiService)
    {
        $this->apiService = $mailerliteApiService;
    }

    /**
     * @return View
     * Loads initial page with the api key authorization form
     */
    public function index(): View
    {
        return view('index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * Received submitted key and attempts  to check if it is valid.
     * If the key is valid, it is saved in the database.
     * If it is not valid the user is redirected back to the authorization page
     * and shown an error message
     */
    public function validateKey(Request $request): RedirectResponse
    {
        $this->validate($request, ['api_key' => 'required|string']);
        if ($this->apiService->validateApiKey($request->api_key)) {
            ApiUser::create($request->only('api_key'));
            return redirect()->route('subscribers');
        }
        return redirect()->route('index')->with('error', 'Please enter a valid API Key');
    }
}
