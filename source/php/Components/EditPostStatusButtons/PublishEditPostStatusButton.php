<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

use \VolunteerManager\Helper\Admin\UrlBuilderInterface as UrlBuilderInterface;

class PublishEditPostStatusButton implements EditPostStatusButton
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
            $this->urlBuilder->createPostActionUrl('update_post_status', ['post_id' => $this->postId, 'post_status' => 'draft']),
            'button-primary button-primary__red',
            __('Unpublish this post', 'api-volunteer-manager'),
            __('Unpublish', 'api-volunteer-manager')
        );
    }
}