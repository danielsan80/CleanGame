<?php

namespace Dan\CleanGame\Model;

class TeamManager
{
    const BASE_PATH = '/../../../..';
    
    private $activityManager;
    private $store;
    
    
    public function setActivityManager($activityManager) {
        $this->activityManager = $activityManager;
    }
    
    public function setStore($store) {
        $this->store = $store;
    }
    
    public function getTeams()
    {
        $activities = $this->activityManager->getDoneActivities();
        $teamsPoints = array();
        
        foreach ($activities as $activity) {
            $owner = $activity->getOwner();
            if (!isset($teamsPoints[$owner])) {
                $teamsPoints[$owner] = 0;
            }
            $teamsPoints[$owner] += $activity->getPoints();
        }
        
        
        $data = $this->store->getData();
        $teams = array();
        foreach ($data as $i => $item) {
            $team = new Team($item);
            if (isset($teamsPoints[$team->getName()])) {
                $team->setPoints($teamsPoints[$team->getName()]/$team->getNumber());
            }
            
            $teams[] = $team;
        }
        
        usort($teams, array('self', 'compareTeams'));
        $teams = array_reverse($teams);
        
        return $teams;
    }
    
    private function compareTeams($team1, $team2)
    {
        
        if ($team1->getPoints() == $team2->getPoints()) {
            return 0;
        }
        return ($team1->getPoints() > $team2->getPoints()) ? +1 : -1;
    }
    
    public function find($name) {
        return new Team($this->store->getData($name) );
    }
    
    public function save(Team $team) {
        
        $this->store->setEntityData($team->toArray());
    }
    
}