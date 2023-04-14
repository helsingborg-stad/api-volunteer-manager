<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

use VolunteerManager\Helper\Admin\UI as AdminUI;

class AssignmentApplicationMetaBox extends ApplicationMetaBox
{
    /**
     * Renders a list of applications assigned to a particular post.
     * @param object $post
     * @return void
     */
    public function getApplicationRow(object $post): string
    {
        $employee = get_field('application_employee', $post->ID);
        $date = get_the_date('y-m-d H:i', $post->ID);
        $status = get_field('application_status', $post->ID);
        return
            '<tr>
                <td class="title"><a href="' . get_edit_post_link($employee->ID) . '">' . $employee->post_title . '</a></td>
                <td>' . $date . '</td>
                <td>' . AdminUI::createTaxonomyPills([$status]) . '</td>
                <td class="actions">
                    <a href="' . get_edit_post_link($post->ID) . '">' . __('Edit', AVM_TEXT_DOMAIN) . '</a>
                    <a href="' . get_delete_post_link($post->ID) . '" class="delete">' . __('Delete', AVM_TEXT_DOMAIN) . '</a>
                    </td>
            </tr>';
    }
}