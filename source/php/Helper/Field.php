<?php

namespace VolunteerManager\Helper;

class Field
{
    public static function get($selector, $id, $formatValue = true)
    {
      return get_field($selector, $id, $formatValue);
    }

    public static function update($selector, $value, $id)
    {
        return update_field($selector, $value, $id);
    }
}
