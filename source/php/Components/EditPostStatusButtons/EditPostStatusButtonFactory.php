<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

class EditPostStatusButtonFactory
{
    /**
     * Factory that creates edit post status button
     *
     * @param integer $postId
     * @param string $postStatus
     * @return EditPostStatusButton
     */
    public static function create(int $postId, string $postStatus): EditPostStatusButton
    {
        switch ($postStatus) {
            case 'trash':
            case 'draft':
                return new DraftEditPostStatusButton($postId);
            case 'publish':
                return new PublishEditPostStatusButton($postId);
            case 'pending':
            default:
                return new PendingEditPostStatusButton($postId);
        }
    }
}
