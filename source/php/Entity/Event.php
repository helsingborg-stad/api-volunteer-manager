<?php

namespace VolunteerManager\Entity;

class Event {

  protected $eventType;
  
  public function __construct($eventType) {
    $this->eventType = $eventType;
  }

  public function taxonomyTo($taxonomy, $to, $callback = null) {
    /** Trigger something when taxonomyName, enters value in $to */
  }

  public function taxonomyFrom($taxonomy, $from, $callback = null) {
    /** Trigger something when taxonomyName, leaves value in $from */
  }
}