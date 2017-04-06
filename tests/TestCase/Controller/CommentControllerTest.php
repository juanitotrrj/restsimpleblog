<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CommentController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\CommentController Test Case
 */
class CommentControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * Test if the URI is active
     * @return void
     */
    public function testCommentsApiAccess()
    {
        $this->get('/comment/blog/131');
        $this->assertResponseOk();
        $this->assertResponseCode(200);
    }

    /**
     * Test if the return is always JSON
     * @return void
     */
    public function testCommentsJsonReturn()
    {
        $this->get('/comment/blog/131');
        $this->assertHeaderContains('Content-Type', 'application/json');
    }

    /**
     * Accept only POST HTTP verb on adding commments
     * @return void
     */
    public function testCommentsAcceptPostOnlyOnSave()
    {
        // Correct
        $this->post('/comment/add', ['m' => 'test' . time(), 'id' => 131]);
        $this->assertResponseOk();

        // Wrong
        $this->get('/comment/add', ['m' => 'test' . time(), 'id' => 131]);
        $this->assertResponseCode(405);
        $this->put('/comment/add', ['m' => 'test' . time(), 'id' => 131]);
        $this->assertResponseCode(405);
        $this->delete('/comment/add', ['m' => 'test' . time(), 'id' => 131]);
        $this->assertResponseCode(405);
        $this->patch('/comment/add', ['m' => 'test' . time(), 'id' => 131]);
        $this->assertResponseCode(405);
    }

    /**
     * Accept only GET on retrieving comments
     * @return void
     */
    public function testCommentsAcceptGetOnlyOnRetrieve()
    {
        // Correct
        $this->get('/comment/blog/131');
        $this->assertResponseOk();
        $this->assertHeaderContains('Content-Type', 'application/json');

        // Wrong
        $this->post('/comment/blog/131');
        $this->assertResponseCode(405);
        $this->put('/comment/blog/131');
        $this->assertResponseCode(405);
        $this->delete('/comment/blog/131');
        $this->assertResponseCode(405);
        $this->patch('/comment/blog/131');
        $this->assertResponseCode(405);
    }
}
