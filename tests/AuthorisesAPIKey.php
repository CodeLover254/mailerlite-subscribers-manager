<?php
namespace Tests;

trait AuthorisesAPIKey
{
    private function authoriseAPIKey($apiKey){
        $this->post('/',['api_key'=>$apiKey]);
    }
}
