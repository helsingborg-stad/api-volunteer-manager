<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

use \VolunteerManager\Helper\Admin\UrlBuilderInterface as UrlBuilderInterface;

class DraftEditPostStatusButton implements EditPostStatusButton
{
    private int $postId;
    private UrlBuilderInterface $urlBuilder;

    public function __construct(int $postId, UrlBuilderInterface $urlBuilder)
    {
        $this->postId = $postId;
        $this->urlBuilder = $urlBuilder;
    }

    public function getHtml(): string
    {
        $format = '<a href="%s" class="%s" title="%s">%s</a>';
        return sprintf(
            $format,
            $this->urlBuilder->createPostActionUrl('update_post_status', ['post_id' => $this->postId, 'post_status' => 'publish']),
            'button-primary',
            __('Publish this post', 'api-volunteer-manager'),
            __('Publish', 'api-volunteer-manager')
        );
    }
}
