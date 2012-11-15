<?php

use Dan\CleanGame\Model;

class ActivityManager
{
    private $guzzleClient;
    private $googleConfig;
    
    public function setGuzzleClient($client) {
        $this->guzzleClient = $client;
    }
  
    public function setGoogleConfig($config) {
        $this->googleConfig = $config;
    }
    
    public function getActivities()
    {
        $client = $this->guzzleClient;
        $config = $this->googleConfig;
        $client->setBaseUrl('https://www.googleapis.com/calendar/v3?key='.$config['client']['developerKey']);
        $request = $client->get('calendars/'.$config['calendar']['id'].'/events');
        $query = $request->getQuery();
        $start = new \DateTime('-2 weeks');
        //$end = new \DateTime('+2 weeks');
        $query->set('timeMin', $start->format('Y-m-d\TH:i:s.000P'));
        //$query->set('timeMax', $end->format('Y-m-d\TH:i:s.000P'));
        $query->set('orderBy', 'startTime');
        $query->set('singleEvents', 'true');
        $response = $request->send();
        $calendar = json_decode($response->getBody(true));
        $items = $calendar->items;
        $cleanGameItems = array();
        foreach ($items as $i => $item) {
            if (preg_match('/\[cleangame\]/', $item->summary)) {
                $cleanGameItems[] = $item;
            }
        }
        
        return $cleanGameItems;
    }

}