<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\AuthorisesAPIKey;
use Tests\DeletesTestGroupAfterTestComplete;
use Tests\TestCase;

class GroupFeatureTest extends TestCase
{
    use DatabaseMigrations;
    use AuthorisesAPIKey;
    use DeletesTestGroupAfterTestComplete;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authoriseAPIKey(config('mailerlite.test-api-key'));
    }


    /**
     * tests whether the correct view with a form to add a group is displayed
     *
     * @return void
     */
    public function test_loads_correct_view_when_group_is_not_added_yet()
    {
        $response = $this->get(route('show-add-group'));
        $response->assertStatus(200);
        $response->assertViewIs('create-group');
    }

    /**
     * test whether form validation works when an empty group name is submitted
     */
    public function test_form_validation_fails_when_group_name_is_empty()
    {
        $response = $this->post(route('store-group',['group_name'=>null]));
        $response->assertSessionHasErrors(['group_name']);
    }

    /**
     * test whether a group is created successfully and stored in the database
     */
    public function test_successfully_creates_group()
    {
        $response = $this->post(route('store-group',['group_name'=>'Unit Test Group']));
        $response->assertSessionHas(['status'=>true,'message'=>'Operation Successful']);
        $this->assertDatabaseHas(Group::class,['id'=>1,'group_name'=>'Unit Test Group']);
        $this->deleteTestGroup(Group::first());
    }

}
