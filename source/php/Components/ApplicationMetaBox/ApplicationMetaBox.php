<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

abstract class ApplicationMetaBox implements ApplicationMetaBoxInterface
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
     * Returns an individual application row
     * @param object $post
     */
    abstract protected function getApplicationRow(object $post): string;
}