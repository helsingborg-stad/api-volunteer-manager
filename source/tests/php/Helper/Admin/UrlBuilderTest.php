<?php

namespace VolunteerManager\Helper\Admin;

use PluginTestCase\PluginTestCase;
use VolunteerManager\Helper\Admin\UrlBuilder as UrlBuilder;

class UrlBuilderTest extends PluginTestCase
{
    public function testCreatePostActionUrl()
    {
        $url = new UrlBuilder(fn() => 'https://www.example.com', fn() => 'test-nonce');
        $this->assertEquals(
            'https://www.example.com?nonce=test-nonce&action=test-action',
            $url->createPostActionUrl('test-action', array())
        );
    }
}