<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

use VolunteerManager\Helper\Admin\UrlBuilderInterface as UrlBuilderInterface;

class EditPostStatusButtonFactory
{
    /**
     * Factory that creates edit post status button
     *
     * @param integer             $postId
     * @param string              $postStatus
     * @param UrlBuilderInterface $urlBuilder
     * @return EditPostStatusButton
     */
    public static function create(int $postId, string $postStatus, UrlBuilderInterface $urlBuilder): EditPostStatusButton
    {
        switch ($postStatus) {
            case 'trash':
            case 'draft':
                return new DraftEditPostStatusButton($postId, $urlBuilder);
            case 'publish':
                return new PublishEditPostStatusButton($postId, $urlBuilder);
            case 'pending':
            default:
                return new PendingEditPostStatusButton($postId);
        }
    }
}
