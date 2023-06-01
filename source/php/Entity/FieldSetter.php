<?php

namespace VolunteerManager\Entity;

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
}
