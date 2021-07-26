<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\AuthorisesAPIKey;
use Tests\BuildsTestGroup;
use Tests\DeletesTestGroupAfterTestComplete;
use Tests\TestCase;

class SubscribersFeatureTest extends TestCase
{
    use DatabaseMigrations;
    use AuthorisesAPIKey;
    use BuildsTestGroup;
    use DeletesTestGroupAfterTestComplete;

    private $testUserProfile =[
        'name'=>'Test User',
        'email'=>'testuser@example.com',
        'country'=>'Canada'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->authoriseAPIKey(config('mailerlite.test-api-key'));
        $this->createTestGroup();
    }

    /**
     * test if the correct view is loaded
     *
     * @return void
     */
    public function test_loads_subscribers()
    {
        $response = $this->get(route('subscribers'));
        $response->assertStatus(200);
        $response->assertViewIs('subscribers');
    }

    /**
     * test if the correct view with the add subscriber form is displayed
     */
    public function test_shows_subscriber_create_form()
    {
        $response = $this->get(route('show-add-subscriber'));
        $response->assertStatus(200);
        $response->assertViewIs('create-subscriber');
    }


    /**
     * test if validation fails when an empty subscriber list is submitted
     */
    public function test_validation_fails_if_subscriber_info_is_missing()
    {
        $response = $this->post(route('store-subscriber'),[]);
        $response->assertSessionHas(['errors']);
    }

    /**
     * tests if a subscriber is stored successfully
     */
    public function test_successfully_creates_subscriber_with_all_info()
    {
        $response = $this->post(route('store-subscriber'),$this->testUserProfile);
        $response->assertSessionHas(['status'=>true,'message'=>'Operation Successful']);

    }

    /**
     * tests if the subscriber edit form is displayed with the correct subscriber information
     */
    public function test_shows_subscriber_edits_form()
    {
        $email=$this->testUserProfile['email'];
        $response = $this->get(route('show-edit-subscriber',['email'=>$email]));
        $response->assertViewIs('update-subscriber');
        $response->assertViewHas('response',function($resp) use ($email){
            return array_key_exists('data',$resp) && $resp['data']->email == $email ;
        });
    }

    /**
     * test if a non existed subscriber would fail
     */
    public function test_fails_to_load_subscriber_if_invalid_email_is_given()
    {
        $response = $this->get(route('show-edit-subscriber',['email'=>'noneexistent@example.com']));
        $response->assertRedirect(route('subscribers'));
        $response->assertSessionHas(['status'=>false,'message'=>'Subscriber not found']);
    }

    /**
     * tests if a valid subscriber is successfully updated
     */
    public function test_successfully_updates_subscriber()
    {
        $response = $this->post(route('update-subscriber',['email'=>$this->testUserProfile['email']]),['name'=>'Tester Updated','country'=>'USA']);
        $response->assertRedirect(route('subscribers'));
        $response->assertSessionHas(['status'=>true,'message'=>'Operation Successful']);
    }

    /**
     * tests if a subscriber is successfully deleted
     */
    public function test_successfully_deletes_subscriber()
    {
        $response = $this->post(route('delete-subscriber'),['email'=>$this->testUserProfile['email']]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status','message']);
    }

    protected function tearDown(): void
    {
        $this->deleteTestGroup(Group::first());
        parent::tearDown();
    }
}
