<?php

namespace VolunteerManager\Helper;

class Url
{
  public static function current()
  {
    $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $currentURL .= $_SERVER["SERVER_NAME"];

    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $currentURL .= ":".$_SERVER["SERVER_PORT"];
    }

    $currentURL .= $_SERVER["REQUEST_URI"];

    return rtrim($currentURL, "/");
  }
}
