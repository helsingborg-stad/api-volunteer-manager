<?php

namespace VolunteerManager\Helper;

class Field
{
    public static function get($selector, $id, $formatValue = true)
    {
      return get_field($selector, $id, $formatValue);
    }
}
