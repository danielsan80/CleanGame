<?php

namespace Dan\CleanGame\Model;

class TeamManager
{
    const BASE_PATH = '/../../../..';
    
    private $dataPath;
    private $activityManager;
    
    public function setDataPath($path) {
        $this->dataPath = $path;
    }
    
    public function setActivityManager($activityManager) {
        $this->activityManager = $activityManager;
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
        
        
        $data = $this->getTeamsData();
        $teams = array();
        foreach ($data as $i => $item) {
            $team = new Team(get_object_vars($item));
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
        return new Team($this->getTeamData($name) );
    }
    
    public function save(Team $team) {
        
        $this->setTeamData($team);
    }
    
    private function getTeamsDataFilename()
    {
        return __DIR__.self::BASE_PATH.$this->dataPath.'/teams.json';
    }
    
    private function getTeamsData() {
        $dataFile = $this->getTeamsDataFilename();
        if (file_exists($dataFile)) {
            $data = json_decode(file_get_contents($dataFile));
        } else {
            $data = array();
        }
        
        return $data;
    }
    
    private function getTeamData($name) {
        $data = $this->getTeamsData();
        foreach($data as $item) {
            if ($item->name == $name) {
                return get_object_vars($item);
            }
        }
        return null;
    }
    
    private function setTeamData(Team $team) {
        $data = $this->getTeamsData();
        foreach($data as $i => $item ){
            if ($item->name == $team->getName()) {
                $data[$i] = $team->toArray();
                $this->setTeamsData($data);
                return;
            }
        }
        $data[] = $team->toArray();
        $this->setTeamsData($data);
    }
    
    private function setTeamsData($data) {
        $dataFile = $this->getTeamsDataFilename();
        
        file_put_contents($dataFile, json_encode($data));
    }
    
}