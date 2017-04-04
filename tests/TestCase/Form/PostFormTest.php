<?php
namespace App\Test\TestCase\Form;

use App\Form\PostForm;
use Cake\TestSuite\TestCase;

/**
 * App\Form\PostForm Test Case
 */
class PostFormTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Form\PostForm
     */
    public $Post;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Post = new PostForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Post);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
