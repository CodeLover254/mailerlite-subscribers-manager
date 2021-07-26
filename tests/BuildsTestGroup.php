<?php

namespace Tests;

trait BuildsTestGroup
{
    public function createTestGroup()
    {
        $this->post(route('store-group',['group_name'=>'Unit Test Group-'.rand(1000,8348)]));
    }
}
