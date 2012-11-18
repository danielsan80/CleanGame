<?php

namespace Dan\CleanGame\Model;

class ActivityManager
{
    const BASE_PATH = '/../../../..';
    
    private $guzzleClient;
    private $googleConfig;
    private $store;
    
    public function setGuzzleClient($client) {
        $this->guzzleClient = $client;
    }
  
    public function setGoogleConfig($config) {
        $this->googleConfig = $config;
    }
    
    public function setStore($store) {
        $this->store = $store;
    }
    
    public function getCurrentActivities()
    {
        $client = $this->guzzleClient;
        $config = $this->googleConfig;
        $client->setBaseUrl('https://www.googleapis.com/calendar/v3?key='.$config['client']['developerKey']);
        $request = $client->get('calendars/'.$config['calendar']['id'].'/events');
        $query = $request->getQuery();
        $start = new \DateTime('-2 weeks');
        $query->set('timeMin', $start->format('Y-m-d\TH:i:s.000P'));
        $query->set('orderBy', 'startTime');
        $query->set('singleEvents', 'true');
        $query->set('maxResults', 20);
        $response = $request->send();
        $calendar = json_decode($response->getBody(true));
        $events = isset($calendar->items)?$calendar->items:array();
        $activities = array();
        foreach ($events as $i => $event) {
            $activities[] = new Activity($event, $this->store->getEntityData($event->id));
        }
        
        return $activities;
    }
    
    public function getDoneActivities()
    {
        $client = $this->guzzleClient;
        $config = $this->googleConfig;
        $client->setBaseUrl('https://www.googleapis.com/calendar/v3?key='.$config['client']['developerKey']);
        $request = $client->get('calendars/'.$config['calendar']['id'].'/events');
        $query = $request->getQuery();
        $end = new \DateTime('+2 month');
        $query->set('timeMax', $end->format('Y-m-d\TH:i:s.000P'));
        $query->set('orderBy', 'startTime');
        $query->set('singleEvents', 'true');
        $response = $request->send();
        $calendar = json_decode($response->getBody(true));
        $events = isset($calendar->items)?$calendar->items:array();
        $activities = array();
        foreach ($events as $i => $event) {
            $activity = new Activity($event, $this->store->getEntityData($event->id));
            if ($activity->isDone()) {
                $activities[] = $activity;
            }
        }
        
        return $activities;
    }
    
    public function find($id) {
        $client = $this->guzzleClient;
        $config = $this->googleConfig;
        $client->setBaseUrl('https://www.googleapis.com/calendar/v3?key='.$config['client']['developerKey']);
        $request = $client->get('calendars/'.$config['calendar']['id'].'/events/'.$id);
        $response = $request->send();
        $event = json_decode($response->getBody(true));
        return new Activity($event, $this->store->getEntityData($event->id) );
    }
    
    public function save(Activity $activity) {
        
        $this->store->setEntityData($activity->toArray());
    }
    
}