<?php

namespace VolunteerManager;

use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Employee\IEmployeeApiValidator;
use \VolunteerManager\Helper\Url as Url;
use WP_Error;

class Api
{
    private $postTypes = [
        'assignment',
        'employer',
        'employee'
    ];

    private $removableResponseKeys = [
        'author',
        'guid',
        'link',
        'template',
        'meta',
        'taxonomy',
        'menu_order',
        'parent',
        'modified',
        'date'
    ];

    private $renameResponseKeys = [
        'acf' => 'meta',
        'date_gmt' => 'created',
        'modified_gmt' => 'modified'
    ];

    private $disallowedLinkKeys = [
        'self',
        'collection',
        'version-history',
        'predecessor-version',
        'https://api.w.org/attachment',
        'curies',
        'about'
    ];

    private $responseKeysOrder = [
        'id',
        'created',
        'modified',
        'status',
        'type',
        'slug'
    ];

    private $doNotIncludeInSignature = [
        'modified',
        'created'
    ];

    public function __construct()
    {
        //Actions
        add_action('template_redirect', array($this, 'redirectToApi'));
        
        //Filter data output
        if(is_iterable($this->postTypes)) {
            foreach($this->postTypes as $postType) {
                add_filter('rest_prepare_' . $postType, array($this, 'removeLinks'), 10000, 3);
                add_filter('rest_prepare_' . $postType, array($this, 'removeResponseKeys'), 5000, 3);
                add_filter('rest_prepare_' . $postType, array($this, 'renameResponseKeys'), 6000, 3);
                add_filter('rest_prepare_' . $postType, array($this, 'addSignature'), 7000, 3);
                add_filter('rest_prepare_' . $postType, array($this, 'reorderResponseKeys'), 8000, 3);
                add_filter('rest_prepare_' . $postType, array($this, 'useRenderedAsMainValue'), 8000, 3);
            }
        }

        //Remove all endpoints not created by this addon
        add_filter( 'rest_endpoints', array($this, 'removeDefaultEndpoints'));
    }

    /**
     * Remove default endpoints
     *
     * @param array $endpoints
     * @return array
     */
    function removeDefaultEndpoints($endpoints) {
        foreach ($endpoints as $endpoint => $details ) {
            if(in_array($endpoint, ["/", "/wp/v2"])) {
                continue;
            }
            //unset($endpoints[$endpoint]);
        }
        return $endpoints;
    }

    /**
     * Force the usage of WordPress api
     * @return void
     */
    public static function redirectToApi()
    {
        if (php_sapi_name() === 'cli') {
            return;
        }

        if(is_admin()) {
            return;
        }

        if (strpos(Url::current(), rtrim(rest_url(), "/")) === false && Url::current() == rtrim(home_url(), "/")) {
            wp_redirect(rest_url());
            exit;
        }
    }

    /**
     * Rename keys to a more appropriate name
     *
     * @param object    $response  The unfiltered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered response
     */
    public function renameResponseKeys($response, $post, $request)
    {
        $keys = (array) $this->renameResponseKeys;

        if(is_iterable($keys)) {
            foreach($keys as $from => $to) {
                if(array_key_exists($from, $response->data)) {
                    $response->data[$to] = $response->data[$from];
                    unset($response->data[$from]);
                }
            }
        }

        return $response;
    }

    /**
     * If there are rendered key, use that on item level.
     *
     * @param object    $response  The unfiltered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered response
     */
    public function useRenderedAsMainValue($response, $post, $request)
    {
        if(is_iterable($response->data)) {
            foreach($response->data as $key => $item) {
                if(isset($item['rendered'])) {
                    $response->data[$key] = $item['rendered'];
                }
            }
        }
        return $response;
    }

    /**
     * Reorder response keys to a logical order
     *
     * @param object    $response  The unfiltered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered response
     */
    public function reorderResponseKeys($response, $post, $request)
    {
        $response->data = array_replace(
            array_flip($this->responseKeysOrder),
            $response->data
        );
        return $response;
    }

    /**
     * Remove response keys not needed
     *
     * @param object    $response  The unfiltered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered response
     */
    public function removeResponseKeys($response, $post, $request)
    {
        $keys = (array) $this->removableResponseKeys;

        $response->data = array_filter($response->data, function ($k) use ($keys) {
            return !in_array($k, $keys, true);
        }, ARRAY_FILTER_USE_KEY);

        return $response;
    }

    /**
     * Remove links from the reponse
     *
     * @param object    $response  The unfiltered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered response
     */
    public function removeLinks($response, $post, $request) {
        if(is_iterable($response->get_links())) {
            foreach($response->get_links() as $_linkKey => $_linkVal) {
                if(in_array($_linkKey, $this->disallowedLinkKeys)) {
                    $response->remove_link($_linkKey);
                }
            }
        }
        return $response;
    }

    /**
     * Create a unique signature for the given data.
     * Simplifies for other services to know, when
     * data needs to be re-synced.
     *
     * @param object    $response  The unfiltered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered response
     */
    public function addSignature($response, $post, $request) {

        $doNotIncludeInSignature = (array)$this->doNotIncludeInSignature;

        $stack = [];

        if(is_iterable($response->data)) {
            foreach ($response->data as $key => $item) {
                if (!in_array($key, $doNotIncludeInSignature)) {
                    $stack[] = $item;
                }
            }
        }

        $response->data['md5'] = md5(serialize($stack));


        return $response;
    }

    /**
     * Register custom REST API POST endpoints
     *
     * The endpoint is registered in the namespace 'volunteer-manager/v1'.
     * Permissions are set to 'edit_posts' if current user can edit posts.
     *
     * @param string $endpoint The endpoints to register.
     * @param callable $callback The callback function to call when the endpoint is called.
     * @param string $namespace The namespace for the endpoints. Defaults to 'volunteer-manager/v1'
     *
     */
    public function registerPostEndpoint(
        string                $endpoint,
        callable              $callback,
        IEmployeeApiValidator $validator,
        string                $namespace = 'wp/v2'
    ): void
    {
        register_rest_route($namespace, $endpoint, array(
            'methods' => 'POST',
            'callback' => $callback,
            'permission_callback' => function () {
                // return current_user_can('edit_posts');

                return true;
            },
            'args' => array(
                'email' => array(
                    'validate_callback' => function ($param) use ($validator) {
                        return $validator->is_email_unique($param);
                    },
                    'required' => true,
                ),
                'national_identity_number' => array(
                    'validate_callback' => function ($param) use ($validator) {
                        return $validator->is_national_identity_unique($param);
                    },
                    'required' => true,
                ),
            )
        ));
    }
}
