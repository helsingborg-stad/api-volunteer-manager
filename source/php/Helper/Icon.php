<?php 

namespace VolunteerManager\Helper;

class Icon
{
  private static $iconsPath = VOLUNTEER_MANAGER_PATH . "assets/icons/";
  private static $iconsList = [
    'person' => 'person.svg',
  ]; 
  private static $base64Prefix = 'data:image/svg+xml;base64,';

  public static function get($iconName)
  {
    if($fileContents = self::readFile($iconName)) {
      return self::base64Encode($fileContents);
    }
    return false;
  }

  private static function base64Encode($fileContents): string 
  {
    return self::$base64Prefix . base64_encode($fileContents);
  }

  private static function readFile($iconName): string 
  {
    if(!array_key_exists($iconName, self::$iconsList)) {
      return new WP_Error("Could not find icon specified in iconName."); 
    }
    return file_get_contents(
      self::$iconsPath . self::$iconsList[$iconName]
    );
  }
}
