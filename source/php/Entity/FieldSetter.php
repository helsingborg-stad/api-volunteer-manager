<?php

namespace VolunteerManager\Entity;

use VolunteerManager\API\WPResponseFactory;
use WP_REST_Request;

class FieldSetter
{
    public function updateField(string $key, $value, int $postId)
    {
        update_field($key, $value, $postId);
    }

    public function updateFields(int $postId, array $params)
    {
        foreach ($params as $key => $value) {
            $this->updateField($key, $value, $postId);
        }
    }

    public function setPostStatus(int $postId, string $statusSlug, string $taxonomy)
    {
        $statusTerm = get_term_by('slug', $statusSlug, $taxonomy);
        if ($statusTerm) {
            wp_set_post_terms($postId, [$statusTerm->term_id], $taxonomy);
        }
    }

    public function setPostByParam(
        WP_REST_Request $request,
        int             $assignmentId,
        string          $paramKey
    ): void
    {
        $assignmentEligibilityParam = $request->get_param($paramKey);
        $this->setPostStatus(
            $assignmentId,
            $assignmentEligibilityParam,
            'assignment-eligibility'
        );
    }

    /**
     * Saves a media file for a post
     *
     * @param string $key          The key of the file in the $_FILES array.
     * @param int    $postId       The ID of the post to attach the media file to.
     * @param array  $allowedTypes An array of allowed file types.
     *
     * @return int|\WP_Error The attachment ID on success, or a \WP_Error object on failure.
     */
    public function savePostMedia(string $key, int $postId, array $allowedTypes = [])
    {
        $fileType = wp_check_filetype(basename($_FILES[$key]['name']));
        if (!in_array($fileType['type'], $allowedTypes)) {
            $errorMessage = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
            return WPResponseFactory::wp_error_response('invalid_file_type', $errorMessage,);
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload($key, $postId);
        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }
        set_post_thumbnail($postId, $attachment_id);

        return $attachment_id;
    }
}
