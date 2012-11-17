<?php

namespace Dan\CleanGame\Model;

class Team
{
    private $position;
    private $name;
    private $number;
    private $points=0;
    
    public function __construct($data) {
        $this->setName($data['name']);
        $this->setNumber($data['number']);
    }
    
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    public function setName($name)
    {
        $this->name = trim($name);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setNumber($number)
    {
        $this->number = (int)$number;
    }
    
    public function getNumber()
    {
        return $this->number;
    }
    
    public function setPoints($points)
    {
        $this->points = $points;
    }
    
    public function getPoints()
    {
        return $this->points;
    }
    
    public function toArray()
    {
        return array(
          'position' => $this->getPosition(),
          'name' => $this->getName(),
          'number' => $this->getNumber(),
          'points' => $this->getPoints(),
        );
    }
}