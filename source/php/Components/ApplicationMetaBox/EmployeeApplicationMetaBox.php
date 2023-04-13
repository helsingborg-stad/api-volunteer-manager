<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

use VolunteerManager\Helper\Admin\UI as AdminUI;

class EmployeeApplicationMetaBox extends ApplicationMetaBox
{
    /**
     * Renders a list of applications assigned to a particular post.
     * @param object $application
     * @return void
     */
    public function getApplicationRow(object $application): string
    {
        $assignment = get_field('application_assignment', $application->ID);
        $date = get_the_date('y-m-d H:i', $application->ID);
        $status = get_field('application_status', $application->ID);
        return
            '<tr>
                <td class="title"><a href="' . get_edit_post_link($assignment->ID) . '">' . $assignment->post_title . '</a></td>
                <td>' . $date . '</td>
                <td>' . AdminUI::createTaxonomyPills([$status]) . '</td>
                <td class="actions">
                    <a href="' . get_edit_post_link($application->ID) . '">' . __('Edit', AVM_TEXT_DOMAIN) . '</a>
                    <a href="' . get_delete_post_link($application->ID) . '" class="delete">' . __('Delete', AVM_TEXT_DOMAIN) . '</a>
                    </td>
            </tr>';
    }
}