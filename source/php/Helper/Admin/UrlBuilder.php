<?php

namespace VolunteerManager\Helper\Admin;

interface UrlBuilderInterface
{
    public function createPostActionUrl(string $action, array $args): string;
}

class UrlBuilder implements UrlBuilderInterface
{
    private $adminUrl;
    private $createNonce;

    public function __construct(?callable $adminUrl = null, ?callable $createNonce = null)
    {
        $this->adminUrl = $adminUrl ?? fn($path) => admin_url($path);
        $this->createNonce = $createNonce ?? fn($action) => wp_create_nonce($action);
    }

    /**
     * Creates a valid url that triggers admin post hook
     * @param string $action
     * @param array  $args
     * @return string
     */
    public function createPostActionUrl(string $action, array $args): string
    {
        $paged = filter_input(INPUT_GET, 'paged');
        $args = array_merge(
            array(
                'nonce' => call_user_func($this->createNonce, $action),
                'action' => $action,
                'paged' => $paged,
            ),
            $args
        );
        $queryString = http_build_query($args);
        return call_user_func($this->adminUrl, 'admin-post.php') . '?' . $queryString;
    }
}
