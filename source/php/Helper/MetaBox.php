<?php

namespace VolunteerManager\Helper;

class MetaBox
{
    public function remove($slug, $postType, $position = 'side')
    {
        add_action('admin_menu', function () {
            remove_meta_box($slug, $postType, $position);
        });
    }
}
