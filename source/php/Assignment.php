<?php

namespace VolunteerManager;

use VolunteerManager\Components\EditPostStatusButtons\EditPostStatusButtonFactory as EditPostStatusButtonFactory;
use VolunteerManager\Entity\Filter as Filter;
use VolunteerManager\Entity\PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;
use VolunteerManager\Helper\Admin\UrlBuilder as UrlBuilder;
use VolunteerManager\Helper\MetaBox as MetaBox;

class Assignment extends PostType
{
    private Taxonomy $assignmentTaxonomy;

    public function addHooks(): void
    {
        parent::addHooks();
        add_action('admin_post_update_post_status', array($this, 'updatePostStatus'));
        add_action('add_meta_boxes', array($this, 'registerSubmitterMetaBox'), 10, 2);
        add_action('add_meta_boxes', array($this, 'registerApplicationsMetaBox'), 10, 2);
        add_action('init', array($this, 'registerStatusTaxonomy'));
        add_action('init', array($this, 'insertAssignmentStatusTerms'));
        add_action('init', array($this, 'addPostTypeTableColumn'));

        add_filter('avm_notification', array($this, 'populateNotificationSender'), 10, 1);
        add_filter('avm_external_assignment_approved_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_external_assignment_denied_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
    }

    /**
     * Populates notifications with sender email address
     * @param array $args
     * @return array
     */
    public function populateNotificationSender(array $args): array
    {
        $senderOption = get_field('notification_sender', 'option');
        $senderEmail = $senderOption['email'] ?? '';
        $sender = $senderOption['name'] ? "{$senderOption['name']} <{$senderEmail}>" : $senderEmail;
        $args['from'] = $sender;
        return $args;
    }

    /**
     * Populate notification with receiver email address
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithSubmitter(array $args, int $postId): array
    {
        $receiver = get_post_meta($postId, 'submitted_by_email', true);
        $args['to'] = $receiver ?? '';
        return $args;
    }

    /**
     * Adds custom table columns to the specified post type's admin list table.
     *
     * @return void
     */
    public function addPostTypeTableColumn(): void
    {
        $this->addTableColumn(
            'status',
            __('Status', AVM_TEXT_DOMAIN),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId,
                        'assignment-status'
                    )
                );
            }
        );

        $this->addTableColumn(
            'visibility',
            __('Visibility', AVM_TEXT_DOMAIN),
            false,
            function ($column, $postId) {
                $postStatus = get_post_status($postId);
                $editButton = EditPostStatusButtonFactory::create($postId, $postStatus, new UrlBuilder());
                echo $editButton->getHtml();
            }
        );

        $this->addTableColumn(
            'submitted_from',
            __('Submitted from', AVM_TEXT_DOMAIN),
            false,
            function ($column, $postId) {
                echo get_post_meta($postId, 'source', true);
            }
        );
    }

    /**
     * Update post status
     * @return void
     */
    public function updatePostStatus()
    {
        $paged = filter_input(INPUT_GET, 'paged',);
        $nonce = filter_input(INPUT_GET, 'nonce',);
        $postId = filter_input(INPUT_GET, 'post_id');
        $postStatus = filter_input(INPUT_GET, 'post_status');

        $queryString = http_build_query(array(
            'post_type' => $this->postType->slug,
            'paged' => $paged,
        ));

        $redirectUrl = admin_url('edit.php') . '?' . $queryString;

        if (!wp_verify_nonce($nonce, 'edit_post_status')) {
            wp_redirect($redirectUrl);
        }

        $post = get_post($postId, 'ARRAY_A');
        $post['post_status'] = $postStatus;
        wp_update_post($post);

        wp_redirect($redirectUrl);
        exit();
    }

    /**
     * Create status taxonomy
     */
    public function registerStatusTaxonomy()
    {
        //Register new taxonomy
        $this->assignmentTaxonomy = new Taxonomy(
            __('Statuses', 'api-volunteer-manager'),
            __('Status', 'api-volunteer-manager'),
            'assignment-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->assignmentTaxonomy->registerTaxonomy();

        //Remove default UI
        (new MetaBox)->remove(
            "tagsdiv-assignment-status",
            $this->slug,
        );

        //Add filter
        new Filter(
            'assignment-status',
            'assignment'
        );
    }

    /**
     * Register custom meta boxes
     * @return void
     */
    public function registerSubmitterMetaBox($postType, $post)
    {
        if (!$submittedByEmail = get_post_meta($post->ID, 'submitted_by_email', true)) {
            return;
        }
        add_meta_box(
            'submitter-info',
            __('Submitted by', AVM_TEXT_DOMAIN),
            array($this, 'renderSubmitterData'),
            array('assignment'),
            'normal',
            'low',
            array(
                'submittedByEmail' => $submittedByEmail,
                'submittedByPhone' => get_post_meta($post->ID, 'submitted_by_phone', true) ?? null
            )
        );
    }

    /**
     * Displays contact information about the post submitter
     * @param object $post
     * @param array  $args
     * @return void
     */
    public function renderSubmitterData(object $post, array $args): void
    {
        $content = sprintf('<p>%s</p>', __('Contact details of the person who submitted the assignment.', AVM_TEXT_DOMAIN));
        $content .= $args['args']['submittedByEmail'] ? sprintf('<p><strong>%1$s:</strong> <a href="mailto:%2$s">%2$s</a></p>', __('Email', AVM_TEXT_DOMAIN), $args['args']['submittedByEmail']) : '';
        $content .= $args['args']['submittedByPhone'] ? sprintf('<p><strong>%s:</strong> %s</p>', __('Phone', AVM_TEXT_DOMAIN), $args['args']['submittedByPhone']) : '';
        echo $content;
    }

    /**
     * Register employees meta box
     * @return void
     */
    public function registerApplicationsMetaBox($postType, $post)
    {
        add_meta_box(
            'assignment_employees',
            __('Employees', AVM_TEXT_DOMAIN),
            array($this, 'renderEmployeesList'),
            array('assignment'),
            'normal',
            'low',
            ['applications' => $this->getApplications($post)]
        );
    }

    /**
     * Retrieves list of assignments applications
     * @param object $post
     * @param array  $args
     * @return array
     */
    public function getApplications(object $post, array $args = []): array
    {
        return get_posts(
            array_merge([
                'post_type' => 'application',
                'orderby' => 'post_date',
                'order' => 'ASC',
                'posts_per_page' => -1,
                'suppress_filters' => true,
                'meta_query' => [
                    [
                        'key' => 'application_assignment',
                        'value' => $post->ID,
                        'compare' => '='
                    ]
                ],
            ], $args)
        );
    }

    /**
     * Renders a list of employees assigned to a particular post.
     * @param object $post
     * @param array  $args
     * @return void
     */
    public function renderEmployeesList(object $post, array $args): void
    {
        if (empty($args['args']['applications'])) {
            echo '<div class="empty_result">' . __('No employees found.', AVM_TEXT_DOMAIN) . '</div>';
            return;
        }

        $html = '<table>';
        $html .= '<tr>
                    <th>' . __('Name', AVM_TEXT_DOMAIN) . '</th>
                    <th>' . __('Date', AVM_TEXT_DOMAIN) . '</th>
                    <th>' . __('Status', AVM_TEXT_DOMAIN) . '</th>
                    <th></th>
                  </tr>';
        foreach ($args['args']['applications'] as $application) {
            $employee = get_field('application_employee', $application->ID);
            $date = get_the_date('y-m-d H:i', $application->ID);
            $status = get_field('application_status', $application->ID);
            $html .=
                '<tr>
                        <td class="employee_name"><a href="' . get_edit_post_link($employee->ID) . '">' . $employee->post_title . '</a></td>
                        <td>' . $date . '</td>
                        <td>' . AdminUI::createTaxonomyPills([$status]) . '</td>
                        <td class="actions">
                            <a href="' . get_edit_post_link($application->ID) . '">' . __('Edit', AVM_TEXT_DOMAIN) . '</a>
                            <a href="' . get_delete_post_link($application->ID) . '" class="delete">' . __('Delete', AVM_TEXT_DOMAIN) . '</a>
                        </td>
                    </tr>';
        }
        $html .= '</table>';

        echo $html;
    }

    public function insertAssignmentStatusTerms()
    {
        return $this->assignmentTaxonomy->insertTerms(AssignmentConfiguration::getStatusTerms());
    }
}
