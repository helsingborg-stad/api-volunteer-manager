<?php

namespace php\Components;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Components\ApplicationMetaBox\ApplicationMetaBox;

class ApplicationMetaBoxTest extends PluginTestCase
{
    private $applicationMetaBox;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = (object)['ID' => 123, 'post_type' => 'assignment'];

        $this->applicationMetaBox = $this->getMockForAbstractClass(
            ApplicationMetaBox::class,
            [$this->post, 'Title', 'key']
        );
    }

    public function testRegister()
    {
        Functions\expect('add_meta_box')->once();
        Functions\when('get_posts')->justReturn([]);
        $this->applicationMetaBox->register();
    }

    /**
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testGetApplications()
    {
        Functions\expect('get_posts')->once()
            ->with(
                [
                    'post_type' => 'application',
                    'post_status' => 'any',
                    'orderby' => 'post_date',
                    'order' => 'ASC',
                    'posts_per_page' => -1,
                    'suppress_filters' => true,
                    'meta_query' => [
                        [
                            'key' => 'key',
                            'value' => $this->post->ID,
                            'compare' => '='
                        ]
                    ]
                ])->andReturn([]);
        $this->applicationMetaBox->getApplications();
    }

    public function testRenderWithEmptyApplications()
    {
        Functions\Expect('get_field')->never();
        Functions\Expect('get_the_date')->never();

        ob_start();
        $this->applicationMetaBox->render([]);
        $output = ob_get_clean();

        $this->assertStringContainsString('No applications found.', $output);
    }

    public function testRenderWithApplications()
    {
        $applications = [(object)['ID' => 1]];
        $post = (object)['ID' => 99, 'post_title' => 'Foo Bar', 'post_type' => 'assignment'];
        $status = (object)['term_id' => 10, 'taxonomy' => 'tax', 'slug' => 'tax', 'name' => 'Tax'];

        Functions\Expect('get_field')->times(6)->andReturn($post, '1', $post, $status, '#ddd', '#fff');
        Functions\Expect('get_the_date')->times(1)->andReturn('1970-01-01');
        Functions\Expect('get_edit_post_link')->times(3)->andReturn('https://url.com/1/edit.php', 'https://url.com/2/edit.php', 'https://url.com/3/edit.php');
        Functions\Expect('get_delete_post_link')->once()->andReturn('https://url.com/1/delete.php');
        Functions\Expect('get_the_terms')->once()->andReturn([(object)['slug' => '1']]);

        ob_start();
        $this->applicationMetaBox->render($applications);
        $output = ob_get_clean();
        $output = str_replace("\n", "", preg_replace('/\s*(<[^>]*>)\s*/', '$1', $output));

        $expected = '<table><tr><th>Name</th><th>Date</th><th>Eligibility</th><th>Status</th><th></th></tr><tr><td class="title"><a href="https://url.com/1/edit.php">Foo Bar</a></td><td>1970-01-01</td><td><span class="">Level 2</span></td><td><span style="background: #ddd; color: #fff;" class="term-pill term-pill-tax">Tax</span></td><td class="actions"><a href="https://url.com/3/edit.php" >Edit</a><a href="https://url.com/1/delete.php" class="red">Delete</a></td></tr></table>';
        $this->assertEquals($expected, $output);
    }

    public function testGetApplicationRow()
    {
        $post = (object)['ID' => 99, 'post_title' => 'Foo Bar', 'post_type' => 'assignment'];
        $status = (object)['term_id' => 10, 'taxonomy' => 'tax', 'slug' => 'tax', 'name' => 'Tax'];

        Functions\Expect('get_field')->times(6)->andReturn($post, '1', $post, $status, '#ddd', '#fff');
        Functions\Expect('get_the_date')->times(1)->andReturn('1970-01-01');
        Functions\Expect('get_edit_post_link')->times(3)->andReturn('https://url.com/1/edit.php', 'https://url.com/2/edit.php', 'https://url.com/3/edit.php');
        Functions\Expect('get_delete_post_link')->once()->andReturn('https://url.com/1/delete.php');
        Functions\Expect('get_the_terms')->once()->andReturn([(object)['slug' => '1']]);

        $result = $this->applicationMetaBox->getApplicationRow($post);
        $result = str_replace("\n", "", preg_replace('/\s*(<[^>]*>)\s*/', '$1', $result));

        $expected = '<tr><td class="title"><a href="https://url.com/1/edit.php">Foo Bar</a></td><td>1970-01-01</td><td><span class="">Level 2</span></td><td><span style="background: #ddd; color: #fff;" class="term-pill term-pill-tax">Tax</span></td><td class="actions"><a href="https://url.com/3/edit.php" >Edit</a><a href="https://url.com/1/delete.php" class="red">Delete</a></td></tr>';

        $this->assertEquals($expected, $result);
    }
}