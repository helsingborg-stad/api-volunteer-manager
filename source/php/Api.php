<?php

namespace VolunteerManager;

use \VolunteerManager\Helper\Url as Url;

class Api
{
    private $postTypes = [
        'assignment',
        'employer',
        'employee'
    ]; 

    private $removeableResponseKeys = [
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

        //Change REST prefix
        add_filter('rest_url_prefix', array($this, 'apiBasePrefix'), 5000, 1);
        
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
        
        //Remove all endpints not created by this addon
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
     * Rename /wp-json/ to /json/.
     * @return string Returning "json".
     */
    public function apiBasePrefix($prefix)
    {
        return "json";
    }

    /**
     * Force the usage of wordpress api
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
     * @param object    $response  The unfiletered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered respose     
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
     * @param object    $response  The unfiletered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered respose
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
     * @param object    $response  The unfiletered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered respose
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
     * @param object    $response  The unfiletered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered respose
     */
    public function removeResponseKeys($response, $post, $request)
    {
        $keys = (array) $this->removeableResponseKeys;

        $response->data = array_filter($response->data, function ($k) use ($keys) {
            return !in_array($k, $keys, true);
        }, ARRAY_FILTER_USE_KEY);

        return $response;
    }

    /**
     * Remove links from the reponse
     *
     * @param object    $response  The unfiletered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered respose     
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
     * @param object    $response  The unfiletered response
     * @param object    $post      The post currently being filtered
     * @param object    $request   The request data
     * @return object   $response  The filtered respose     
     */
    public function addSignature($response, $post, $request) {

        $doNotIncludeInSignature = (array) $this->doNotIncludeInSignature; 

        $stack = []; 
        if(is_iterable($response->data)) {
            foreach($response->data as $key => $item) {
                if(!in_array($key, $doNotIncludeInSignature)) {
                    $stack[] = $item; 
                }
            }
        }

        //Add new signature
        $response->data['signature'] = md5(serialize($stack)); 

        return $response; 
    }
}
