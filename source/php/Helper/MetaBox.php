<?php

namespace VolunteerManager\Helper;

class MetaBox
{
    private $slug;
    private $postType;
    private $position; 

    public function remove($slug, $postType, $position = 'side')
    {
        $this->slug = $slug;
        $this->postType = $postType;
        $this->position = $position;
        
        add_action('admin_menu', array($this, 'removeMetaBox'));
    }

    public function removeMetaBox() {
        remove_meta_box(
            $this->slug,
            $this->postType,
            $this->position
        );
    }
}
