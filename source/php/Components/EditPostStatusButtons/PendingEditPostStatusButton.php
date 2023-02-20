<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

class PendingEditPostStatusButton implements EditPostStatusButton
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
            get_edit_post_link($this->postId),
            'button',
            __('This post must be reviewed before it can be published.', 'api-volunteer-manager'),
            __('Review required', 'api-volunteer-manager')
        );
    }
}
