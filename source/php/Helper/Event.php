<?php

namespace VolunteerManager\Helper;

class Event
{
  public $postId = null;
  public $key = null;
  public $value = null;
  public $type = null;
  public $callbackFunction = null;
  public $callbackData = [];

  public function __construct(int $postId, string $key, $value, string $type, $callbackFunction) {
    $this->postId = null;
    $this->key = null;
    $this->value = null;
    $this->type = null;
    $this->callbackFunction = null;
    $this->callbackData = [];
  }

  public function register($time) {
    //wp_schedule_event()
    wp_schedule_event(time(), $hook, array($this, 'execute'));
  }

  public function unregister($event) {
    wp_unschedule_event($time, $hook, $args); 
  }

  public function execute() {
    
    $this->setMetaValue(
      $this->postId, 
      $this->key,
      $this->value
    );

    if(is_callable($this->callbackFunction)) {
      call_user_func(
        $this->callbackFunction,
        $this->callbackData ?? null
      ); 
    }
  }

  private function setMetaValue($postId, $key, $value)
  {
    return update_post_meta($postId, $key, $value);
  }
}