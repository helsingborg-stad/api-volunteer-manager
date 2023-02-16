<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

use \VolunteerManager\Helper\Admin\URL as URL;

class PublishEditPostStatusButton implements EditPostStatusButton
{
    private int $postId;

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function getHtml(): string
    {
        $format = '<a href="%s" class="%s" title="%s">%s</a>';
        return sprintf(
            $format,
            URL::createPostActionUrl(
                'update_post_status',
                ['post_id' => $this->postId, 'post_status' => 'draft'],
                ['\VolunteerManager\Helper\Admin\URL', 'wpCreateNonce']
            ),
            'button-primary button-primary__red',
            __('Unpublish this post', 'api-volunteer-manager'),
            __('Unpublish', 'api-volunteer-manager')
        );
    }
}
