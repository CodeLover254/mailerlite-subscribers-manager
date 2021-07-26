<?php


namespace Tests;


use App\Models\Group;
use App\Services\MailerliteApiService;

trait DeletesTestGroupAfterTestComplete
{
    public function deleteTestGroup(Group $group){
        $apiService = new MailerliteApiService();
        $apiService->deleteGroup($group);
    }
}
