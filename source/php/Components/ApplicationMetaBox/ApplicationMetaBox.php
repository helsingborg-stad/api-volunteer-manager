<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

use VolunteerManager\Helper\Admin\UI as AdminUI;

class ApplicationMetaBox
{
    private object $post;
    private string $metaKey;
    private string $title;

    public function __construct($post, $title, $metaKey)
    {
        $this->post = $post;
        $this->title = $title;
        $this->metaKey = $metaKey;
    }

    /**
     * Register meta box
     * @return void
     */
    public function register(): void
    {
        add_meta_box(
            'applications_meta_box',
            $this->title,
            function ($post, $args) {
                $this->render($args['args']['applications']);
            },
            [$this->post->post_type],
            'normal',
            'low',
            ['applications' => $this->getApplications()]
        );
    }

    /**
     * Retrieves list of applications
     * @return array
     */
    public function getApplications(): array
    {
        return get_posts(
            [
                'post_type' => 'application',
                'post_status' => 'any',
                'orderby' => 'post_date',
                'order' => 'ASC',
                'posts_per_page' => -1,
                'suppress_filters' => true,
                'meta_query' => [
                    [
                        'key' => $this->metaKey,
                        'value' => $this->post->ID,
                        'compare' => '='
                    ]
                ],
            ]
        );
    }

    /**
     * Renders a list of applications assigned to a particular post.
     * @param array $posts
     * @return void
     */
    public function render(array $posts): void
    {
        if (empty($posts)) {
            echo '<div class="empty_result">' . __('No applications found.', AVM_TEXT_DOMAIN) . '</div>';
            return;
        }

        $html = '<table>';
        $html .= '<tr>
                    <th>' . __('Name', AVM_TEXT_DOMAIN) . '</th>
                    <th>' . __('Date', AVM_TEXT_DOMAIN) . '</th>
                    <th>' . __('Eligibility', AVM_TEXT_DOMAIN) . '</th>
                    <th>' . __('Status', AVM_TEXT_DOMAIN) . '</th>
                    <th></th>
                  </tr>';
        foreach ($posts as $post) {
            $html .= $this->getApplicationRow($post);
        }
        $html .= '</table>';

        echo $html;
    }

    /**
     * Renders a list of applications assigned to a particular post.
     * @param object $post
     * @return void
     */
    public function getApplicationRow(object $post): string
    {
        $employee = get_field('application_employee', $post->ID);
        $employeeCrimeRecord = get_field('crime_record_extracted', $employee->ID);
        $employeeEligibilityLevel = $employeeCrimeRecord ? 2 : 1;
        $assignment = get_field('application_assignment', $post->ID);
        $assignmentEligibilityLevel = $this->getAssignmentEligibilityLevel($assignment);
        $eligibilityClass = $employeeEligibilityLevel < $assignmentEligibilityLevel ? 'red' : '';
        $date = get_the_date('y-m-d H:i', $post->ID);
        $status = get_field('application_status', $post->ID);

        $employeeHtml = '<td class="title"><a href="' . get_edit_post_link($employee->ID) . '">' . $employee->post_title . '</a></td>';
        $assignmentHtml = '<td class="title"><a href="' . get_edit_post_link($assignment->ID) . '">' . $assignment->post_title . '</a></td>';

        $html = '<tr>';
        if ($this->post->post_type === 'assignment') {
            $html .= $employeeHtml;
        } elseif ($this->post->post_type === 'employee') {
            $html .= $assignmentHtml;
        }
        $html .= '
        <td>' . $date . '</td>
        <td><span class="' . $eligibilityClass . '">' . __('Level', AVM_TEXT_DOMAIN) . ' ' . $employeeEligibilityLevel . '</span></td>
        <td>' . AdminUI::createTaxonomyPills([$status]) . '</td>
        <td class="actions">
            <a href="' . get_edit_post_link($post->ID) . '">' . __('Edit', AVM_TEXT_DOMAIN) . '</a>
            <a href="' . get_delete_post_link($post->ID) . '" class="red">' . __('Delete', AVM_TEXT_DOMAIN) . '</a>
        </td>
        </tr>';

        return $html;
    }

    public function getAssignmentEligibilityLevel($post): int
    {
        $eligibilityTerms = get_the_terms($post->ID, 'assignment-eligibility');
        return isset($eligibilityTerms[0]) ? (int)$eligibilityTerms[0]->slug : 1;
    }
}