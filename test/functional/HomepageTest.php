<?php

use CleanGame\Test\WebTestCase;

class HomepageTest extends WebTestCase
{
    
    public function testHomeView()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        
        $this->assertEquals('hello world', $crawler->filter('h1')->first()->text());
    }

}