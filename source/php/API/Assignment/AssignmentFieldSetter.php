<?php

namespace VolunteerManager\API\Assignment;

use WP_REST_Request;

class AssignmentFieldSetter
{
    public function updateAssignmentFields(int $assignment_id, array $params): void
    {
        foreach ($params as $key => $value) {
            update_field($key, $value, $assignment_id);
        }
    }

    public function setAssignmentStatus(int $assignment_id): void
    {
        $assignment_status_term = get_term_by('slug', 'pending', 'assignment-status');
        if ($assignment_status_term) {
            wp_set_post_terms($assignment_id, [$assignment_status_term->term_id], 'assignment-status');
        }
    }

    public function setAssignmentEligibility(WP_REST_Request $request, int $assignment_id): void
    {
        $assignment_eligibility_param = $request->get_param('assignment_eligibility');
        $assignment_eligibility_term = get_term_by('slug', $assignment_eligibility_param, 'assignment-eligibility');
        if ($assignment_eligibility_term) {
            wp_set_post_terms($assignment_id, [$assignment_eligibility_term->term_id], 'assignment-eligibility');
        }
    }

    public function setAssignmentSource(WP_REST_Request $request, int $assignment_id): void
    {
        // TODO: Remove static request source check.
        $request_source = $request->get_param('source');
        if ($request_source === 'https://www.helsingborg.se') {
            update_field('internal_assignment', false, $assignment_id);
        }
    }

    public function setAssignmentSignupValues(WP_REST_Request $request, int $assignment_id): void
    {
        $signup_methods = get_fields('signup_methods', $assignment_id);
        if (!is_array($signup_methods)) {
            $signup_methods = [];
        }

        // Update signup link.
        $signup_link = $request->get_param('signup_link');
        if (!empty($signup_link)) {
            update_field('signup_link', $signup_link, $assignment_id);

            $signup_methods[] = 'link';
        }

        // Update signup email.
        $signup_email = $request->get_param('signup_email');
        if (!empty($signup_email)) {
            update_field('signup_email', $signup_email, $assignment_id);

            $signup_methods[] = 'email';
        }

        // Update signup phone.
        $signup_phone = $request->get_param('signup_phone');
        if (!empty($signup_phone)) {
            update_field('signup_phone', $signup_phone, $assignment_id);

            $signup_methods[] = 'phone';
        }

        update_field('signup_methods', $signup_methods, $assignment_id);
    }
}
