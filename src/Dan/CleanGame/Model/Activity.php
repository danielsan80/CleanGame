<?php

namespace Dan\CleanGame\Model;

class Activity
{
    private $id;
    private $name;
    private $deadline;
    private $points=0;
    private $done=false;
    private $owner;
    
    public function __construct($item, $data=null) {
        $this->setId($item->id);
        $this->setName(trim(strtr($item->summary, array('[cleangame]'=>''))));
        $this->setDeadline(new \DateTime($item->end->date.' -1 day'));
        
        if ($data) {
            if (isset($data['done'])) {
                $this->setDone($data['done']);
            }
            if (isset($data['owner'])) {
                $this->setOwner($data['owner']);
            }
        }
        if (isset($item->description)) {
            preg_match('/(?P<points>points=[0-9]+)/', $item->description, $matches);
            if (isset($matches['points'])) {
                $this->setPoints($matches['points']);
            }
        }
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }
    
    public function getDeadline()
    {
        return $this->deadline;
    }
    
    public function setPoints($points)
    {
        $this->points = (int)$points;
    }
    
    public function getPoints()
    {
        return $this->points;
    }
    
    public function setDone($done = true)
    {
        if ($done=='1') {
            $done = true;
        }
        if ($done=='0') {
            $done = false;
        }
        $this->done = (bool)$done;
    }
    
    public function isDone()
    {
        return $this->done;
    }
    
    public function setOwner($owner)
    {
        if (!$owner) {
            $owner = null;
        }
        $this->owner = $owner;
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function isExpired()
    {
        return $this->getDeadline() < new \DateTime();
    }
    public function toArray()
    {
        return array(
          'id' => $this->getId(),
          'name' => $this->getName(),
          'deadline' => $this->getDeadline(),
          'done' => $this->isDone(),
          'owner' => $this->getOwner(),
          'expired' => $this->isExpired(),
        );
    }
}