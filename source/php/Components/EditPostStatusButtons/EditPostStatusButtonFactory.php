<?php

namespace VolunteerManager\Components\EditPostStatusButtons;

class EditPostStatusButtonFactory
{
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
