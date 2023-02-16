<?php

namespace VolunteerManager\Helper\Admin;

use VolunteerManager\Helper\Admin\URL as URL;

use Brain\Monkey\Functions;

class URLTest extends \PluginTestCase\PluginTestCase
{
    public function testCreatePostActionUrl()
    {
        Functions\expect('admin_url')->twice();

        $action = 'foo_bar_action';
        $args = array('param1' => 'value1', 'param2' => 'value2');
        $createNonce = fn($action) => $action;

        $expectedUrl = admin_url('admin-post.php') . '?nonce=' . $createNonce($action) . '&action=' . $action . '&param1=value1&param2=value2';

        $URL = new URL();
        $result = $URL->createPostActionUrl($action, $args, $createNonce);

        $this->assertEquals($expectedUrl, $result);
    }

    public function testGetRequestParameter()
    {
        // Test with a parameter that is present in the query string
        Functions\expect('filter_input')->with(INPUT_GET, 'foo', FILTER_SANITIZE_STRING)->andReturn('bar');
        $result = URL::getRequestParameter('foo', 'default');
        $this->assertEquals('bar', $result);
    }

    public function testGetRequestParameterToBeNull()
    {
        // Test with a parameter that is not present in the query string
        Functions\expect('filter_input')->with(INPUT_GET, 'foo', FILTER_SANITIZE_STRING)->andReturn(null);
        $result = URL::getRequestParameter('foo', 'default');
        $this->assertEquals('default', $result);
    }
}