<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

abstract class ApplicationMetaBox implements ApplicationMetaBoxInterface
{
    private \WP_Post $post;
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
            [$this, 'render'],
            [$this->post->post_type],
            'normal',
            'low',
            ['applications' => $this->getApplications($this->post->ID)]
        );
    }

    /**
     * Retrieves list of applications
     * @param $postId
     * @return array
     */
    public function getApplications($postId): array
    {
        return get_posts(
            [
                'post_type' => 'application',
                'orderby' => 'post_date',
                'order' => 'ASC',
                'posts_per_page' => -1,
                'suppress_filters' => true,
                'meta_query' => [
                    [
                        'key' => $this->metaKey,
                        'value' => $postId,
                        'compare' => '='
                    ]
                ],
            ]
        );
    }

    /**
     * Renders a list of applications assigned to a particular post.
     * @param object $post
     * @param array  $args
     * @return void
     */
    public function render(object $post, array $args): void
    {
        if (empty($args['args']['applications'])) {
            echo '<div class="empty_result">' . __('No applications found.', AVM_TEXT_DOMAIN) . '</div>';
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
            $html .= $this->getApplicationRow($application);
        }
        $html .= '</table>';

        echo $html;
    }

    /**
     * Renders an individual application row
     * @param \WP_Post $application
     */
    abstract protected function getApplicationRow(\WP_Post $application): string;
}