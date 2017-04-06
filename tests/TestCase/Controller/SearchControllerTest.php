<?php
namespace App\Test\TestCase\Controller;

use App\Controller\SearchController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\SearchController Test Case
 */
class SearchControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * Test if home page is accessible
     *
     * @return void
     */
    public function testHomePageAccess()
    {
        // If accessible, ok
        $this->get('/search');
        $this->assertResponseOk();
        $this->assertResponseCode(200);

        // Verify redirect to /search from /
        $this->get('/');
        $this->assertResponseCode(301);
        $this->assertRedirect('/search');
    }

    /**
     * Test if the expected view vars are present
     * @return void
     */
    public function testHomePageViewVars()
    {
        $this->get('/search');
        $this->assertTrue($this->viewVariable('results') !== null);
        $this->assertTrue($this->viewVariable('users') !== null);
        $this->assertTrue($this->viewVariable('post_data') !== null);
        
        $this->assertTrue(isset($this->viewVariable('post_data')['user']));
        $this->assertTrue(isset($this->viewVariable('post_data')['search']));
        $this->assertTrue(isset($this->viewVariable('post_data')['published_date']));
        $this->assertTrue(isset($this->viewVariable('post_data')['sb']));
        $this->assertTrue(isset($this->viewVariable('post_data')['sd']));
    }

    /**
     * Test if it searches by `published_date`
     * @return void
     */
    public function testSearchByPublishedDate()
    {
        // Correct format
        $this->post('/search', [
            'published_date' => '01/24/2017',
            'search' => '',
            'user' => ''
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));

        // Wrong format
        $this->post('/search', [
            'published_date' => 'mm/dd/yyyy',
            'search' => '',
            'user' => ''
        ]);
        $this->assertEmpty($this->viewVariable('results'));
    }
    
    /**
     * Test if it searches by `title` or `content`
     * @return void
     */
    public function testSearchByTitleOrContent()
    {
        // With existing string
        $this->post('/search', [
            'published_date' => '',
            'search' => 'Angkor Wat',
            'user' => ''
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));

        // Without existing string
        $this->post('/search', [
            'published_date' => '',
            'search' => uniqid(),
            'user' => ''
        ]);
        $this->assertEmpty($this->viewVariable('results'));
    }

    /**
     * Test if it searches by comment `author` (/user `id`)
     * @return void
     */
    public function testSearchByCommentAuthor()
    {
        // Existing user
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => 2
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));

        // Invalid user
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => uniqid()
        ]);
        $this->assertEmpty($this->viewVariable('results'));
    }
    
    /**
     * Test if search sorts by `title`
     * @return void
     */
    public function testSortByTitle()
    {
        // Asc
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => '',
            'sb' => 'title',
            'sd' => 'asc'
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));
        
        // Desc
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => '',
            'sb' => 'title',
            'sd' => 'desc'
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));

        // With filters
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => 2,
            'sb' => 'title',
            'sd' => 'asc'
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));
    }

    /**
     * Test if it sorts by published date (/posts `date`)
     * @return [type] [description]
     */
    public function testSortByPublishedDate()
    {
        // Asc
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => '',
            'sb' => 'date',
            'sd' => 'asc'
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));
        
        // Desc
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => '',
            'sb' => 'date',
            'sd' => 'desc'
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));

        // With filters
        $this->post('/search', [
            'published_date' => '',
            'search' => '',
            'user' => 2,
            'sb' => 'date',
            'sd' => 'asc'
        ]);
        $this->assertNotEmpty($this->viewVariable('results'));
    }
    
    /**
     * Test if the `Create new` page for creating new posts is accessible
     * @return void
     */
    public function testCreateNewAccess()
    {
        $this->get('/blog/add');
        $this->assertResponseOk();
    }
}
