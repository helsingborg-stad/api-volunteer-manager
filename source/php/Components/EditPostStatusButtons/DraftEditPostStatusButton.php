<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

use \VolunteerManager\Helper\Admin\UI as UI;

class DraftEditPostStatusButton implements EditPostStatusButton
{
    private $postId;

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function getHtml(): string
    {
        $format = '<a href="%s" class="%s" title="%s">%s</a>';
        return sprintf(
            $format,
            UI::createEditStatusUrl($this->postId, 'publish'),
            'button-primary',
            __('Publish this post', 'api-volunteer-manager'),
            __('Publish', 'api-volunteer-manager')
        );
    }
}
