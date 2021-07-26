<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class IndexViewTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * test if the correct view is loaded
     */
    public function test_loads_correct_view_without_api_key()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('index');
    }

    /**
     * test if validation fails when an empty api key is posted
     */
    public function test_validation_fails_if_empty_api_key_is_submitted()
    {
        $response = $this->post('/',['api_key'=>null]);
        $response->assertSessionHasErrors(['api_key']);
    }

    /**
     * tests if the correct error message is returned when the wrong api key is used
     */
    public function test_authorization_fails_using_wrong_api_key()
    {
        $response = $this->post('/',['api_key'=>'1234af56df9a']);
        $response->assertRedirect('/');
        $response->assertSessionHas('error','Please enter a valid API Key');
    }


    /**
     * Tests if redirection occurs and api key is saved when the user authorises with the correct key
     */
    public function test_redirects_to_correct_page_if_correct_api_key_is_used()
    {
        $testAPIKey = config('mailerlite.test-api-key');
        $response = $this->post('/',['api_key'=>$testAPIKey]);
        $response->assertRedirect('/subscribers');
        $this->assertDatabaseHas('api_users',['id'=>1,'api_key'=>$testAPIKey]);
    }
}
