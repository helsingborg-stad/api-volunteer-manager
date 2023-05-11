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
     * @return string
     */
    public function getApplicationRow(object $post): string
    {
        $employee = get_field('application_employee', $post->ID);
        $employeeEligibilityLevel = $this->getEmployeeEligibilityLevel($employee);
        $assignment = get_field('application_assignment', $post->ID);
        $assignmentEligibilityLevel = $this->getAssignmentEligibilityLevel($assignment);
        $eligibilityClass = $employeeEligibilityLevel < $assignmentEligibilityLevel ? 'red' : '';
        $date = get_the_date('y-m-d H:i', $post->ID);
        $status = get_field('application_status', $post->ID);
        $taxonomyPills = !empty($status) ? AdminUI::createTaxonomyPills([$status]) : '';

        $employeeHtml = $this->createApplicationColumnHtml($employee);
        $assignmentHtml = $this->createApplicationColumnHtml($assignment);
        $columnHtml = $this->post->post_type === 'assignment' ? $employeeHtml : $assignmentHtml;

        return sprintf(
            '<tr>%s<td>%s</td><td><span class="%s">%s %d</span></td><td>%s</td><td class="actions">%s %s</td></tr>',
            $columnHtml,
            $date,
            $eligibilityClass,
            __('Level', AVM_TEXT_DOMAIN),
            $employeeEligibilityLevel,
            $taxonomyPills,
            $this->createActionLink('Edit', $post->ID),
            $this->createActionLink('Delete', $post->ID, 'red')
        );
    }

    /**
     * Creates the HTML for an application column.
     * @param object $application
     * @return string
     */
    private function createApplicationColumnHtml(object $application): string
    {
        return sprintf(
            '<td class="title"><a href="%s">%s</a></td>',
            get_edit_post_link($application->ID),
            $application->post_title
        );
    }

    /**
     * Creates an action link HTML element.
     * @param string      $label
     * @param int         $postId
     * @param string|null $class
     * @return string
     */
    private function createActionLink(string $label, int $postId, ?string $class = null): string
    {
        $url = '';
        switch ($label) {
            case 'Edit':
                $url = get_edit_post_link($postId);
                break;
            case 'Delete':
                $url = get_delete_post_link($postId);
                break;
        }
        $classAttr = $class ? sprintf('class="%s"', $class) : '';
        return sprintf(
            '<a href="%s" %s>%s</a>',
            $url,
            $classAttr,
            esc_html__($label, AVM_TEXT_DOMAIN)
        );
    }

    /**
     * Calculates an employee's eligibility level
     * @param object $employee An object representing the employee.
     * @return int The eligibility level, which can be either 1 (eligible) or 2 (ineligible).
     */
    private function getEmployeeEligibilityLevel(object $employee): int
    {
        $employeeCrimeRecord = get_field('crime_record_extracted', $employee->ID);
        return $employeeCrimeRecord ? 2 : 1;
    }

    /**
     * Gets the eligibility level for an assignment.
     * @param object $post The post object for the assignment.
     * @return int The eligibility level, which can be either 1 (eligible) or a higher value indicating ineligibility.
     */
    private function getAssignmentEligibilityLevel($post): int
    {
        $eligibilityTerms = get_the_terms($post->ID, 'assignment-eligibility');
        return isset($eligibilityTerms[0]) ? (int)$eligibilityTerms[0]->slug : 1;
    }
}