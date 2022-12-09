<?php

namespace VolunteerManager\Helper;

class MetaBox
{
    private static $slug;
    private static $postType;
    private static $position; 

    public static function remove($slug, $postType, $position = 'side')
    {
        self::$slug = $slug;
        self::$postType = $postType;
        self::$position = $position;

        add_action('admin_menu', array('\VolunteerManager\Helper\MetaBox', 'removeMetaBox'));
    }

    public static function removeMetaBox() {
        remove_meta_box(
            self::$slug,
            self::$postType,
            self::$position
        );
    }
}
