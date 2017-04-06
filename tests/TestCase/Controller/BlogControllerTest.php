<?php
namespace App\Test\TestCase\Controller;

use App\Controller\BlogController;
use Cake\TestSuite\IntegrationTestCase;
use GuzzleHttp\Client;
use Faker\Factory as Faker;

/**
 * App\Controller\BlogController Test Case
 */
class BlogControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    public function setUp()
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    /**
     * Test blog page access
     * @return void
     */
    public function testBlogPageAccess()
    {
        $this->get('/blog/131');
        $this->assertResponseOk();
        $this->assertResponseCode(200);
    }

    /**
     * Test blog edit page access
     * @return void
     */
    public function testBlogPostEditAccess()
    {
        $this->get('/blog/edit/131');
        $this->assertResponseOk();
        $this->assertResponseCode(200);
    }

    /**
     * Test if the expected view vars are present
     * @return void
     */
    public function testBlogPageViewVars()
    {
        $this->get('/blog/131');

        $this->assertTrue($this->viewVariable('search_data') !== null);
        $this->assertTrue(isset($this->viewVariable('search_data')['user']));
        $this->assertTrue(isset($this->viewVariable('search_data')['search']));
        $this->assertTrue(isset($this->viewVariable('search_data')['published_date']));
        $this->assertTrue(isset($this->viewVariable('search_data')['sb']));
        $this->assertTrue(isset($this->viewVariable('search_data')['sd']));

        $this->assertTrue($this->viewVariable('blog') !== null);
        $this->assertTrue(isset($this->viewVariable('blog')['id']));
        $this->assertTrue(isset($this->viewVariable('blog')['title']));
        $this->assertTrue(isset($this->viewVariable('blog')['date']));
        $this->assertTrue(isset($this->viewVariable('blog')['date_updated']));
        $this->assertTrue(isset($this->viewVariable('blog')['content']));
        $this->assertTrue(isset($this->viewVariable('blog')['author']));
        $this->assertTrue(isset($this->viewVariable('blog')['image']));
        $this->assertTrue(isset($this->viewVariable('blog')['tags']));
        $this->assertTrue(isset($this->viewVariable('blog')['categories']));
        $this->assertTrue(isset($this->viewVariable('blog')['revisions']));
    }

    /**
     * Link back to search page
     * @return void
     */
    public function testBlogPageGoBack()
    {
        $search_filters = [
            'published_date' => '',
            'search' => 'Banaue Rice Terraces',
            'user' => '',
            'sb' => '',
            'sd' => ''
        ];

        $this->post('/blog/131', $search_filters);
        $derived_search_filters = $this->viewVariable('search_data');

        $this->post('/search', $derived_search_filters);
        $this->assertEquals($search_filters, $this->viewVariable('post_data'));
    }

    /**
     * Test the downloading of blog posts
     * @return void
     */
    public function testBlogPostDownloadPdf()
    {
        //$this->get('/blog/download/pdf/131');
        //$this->assertHeader('Content-Type', 'application/pdf');
        //$this->assertResponseOk();
        //$this->assertResponseCode(200);
    }

    /**
     * Test if able to create post
     * @return void
     */
    public function testBlogPostCreate()
    {
        // Viewing from Search Page
        $this->post('/blog/add');
        $this->assertResponseOk();
        $this->assertResponseCode(200);

        // Viewing from URI (typed)
        $this->get('/blog/add');
        $this->assertResponseOk();
        $this->assertResponseCode(200);

        // Create the post
        copy('http://lorempixel.com/grey/800/400/cats/TestBlogPost/', '/tmp/test.jpg');
        $post_data = [
            'image' => [
                'tmp_name' => '/tmp/test.jpg',
                'type' => 'image/jpg',
                'name' => 'testblogpost' . time() . '.jpeg',
            ],
            'a_create' => true,
            'a_title' => $this->faker->city,
            'a_content' => $this->faker->paragraph(10)
        ];
        $this->post('/blog/add', $post_data);

        // After creation, check if it redirects to blog post view page
        $this->assertResponseCode(302);
        
        // Check if the post exists
        $client = new Client(['base_uri' => env('WAPI_BASE_URI', null)]);
        $post = json_decode((string)$client->request('GET', 'posts', ['query' => ['search' => $post_data['a_title']]])->getBody(), true);
        $this->assertTrue(!empty($post));
    }

    /**
     * Preserve search filters, if any, when creation is cancelled
     * @return void
     */
    public function testBlogPostCreateCancelled()
    {
        $search_filters = [
            'published_date' => '',
            'search' => 'Banaue Rice Terraces',
            'user' => '',
            'sb' => '',
            'sd' => ''
        ];

        $this->post('/blog/add', $search_filters);
        $derived_search_filters = $this->viewVariable('search_data');

        $this->post('/search', $derived_search_filters);
        $this->assertEquals($search_filters, $this->viewVariable('post_data'));   
    }

    /**
     * Test if the view vars are present when viewed from URI, Search Page
     * @return void
     */
    public function testBlogPostCreateViewVars()
    {
        // Viewing from Search Page
        $default = ['title' => 'Blog title', 'content' => 'Blog content', 'image' => ''];
        $search_filters = [
            'published_date' => '',
            'search' => 'Banaue Rice Terraces',
            'user' => '',
            'sb' => '',
            'sd' => ''
        ];

        // Check view vars
        $this->post('/blog/add', $search_filters);
        $this->assertEquals($search_filters, $this->viewVariable('search_data'));
        $this->assertEquals($default, $this->viewVariable('blog'));

        // When viewed via URI
        $this->get('/blog/add');
        $this->assertEquals(array_map(function($filter) { return ''; }, $search_filters), $this->viewVariable('search_data'));
        $this->assertEquals($default, $this->viewVariable('blog'));
    }
    
    /**
     * Test deletion of blog post
     * @return void
     */
    public function testBlogPostDelete()
    {
        // Create a post
        copy('http://lorempixel.com/grey/800/400/cats/TestBlogPost/', '/tmp/test.jpg');
        $post_data = [
            'image' => [
                'tmp_name' => '/tmp/test.jpg',
                'type' => 'image/jpg',
                'name' => 'testblogpost' . time() . '.jpeg',
            ],
            'a_create' => true,
            'a_title' => $this->faker->city,
            'a_content' => $this->faker->paragraph(10)
        ];
        $this->post('/blog/add', $post_data);
        $this->assertResponseCode(302);
        
        // Get the post
        $client = new Client(['base_uri' => env('WAPI_BASE_URI', null)]);
        $post = json_decode((string)$client->request('GET', 'posts', ['query' => ['search' => $post_data['a_title']]])->getBody(), true);

        // Then delete
        $this->delete('/blog/delete/' . $post[0]['id']);
        
        // Check if it exists
        $this->get('/blog/' . $post[0]['id']);
        $this->assertResponseCode(403);
    }

    /**
     * Test the view vars on Blog Edit page
     * @return void
     */
    public function testBlogPostEditViewVars()
    {
        $this->get('/blog/edit/131');

        $this->assertTrue($this->viewVariable('search_data') !== null);
        $this->assertTrue($this->viewVariable('blog') !== null);
        $this->assertTrue(isset($this->viewVariable('search_data')['user']));
        $this->assertTrue(isset($this->viewVariable('search_data')['search']));
        $this->assertTrue(isset($this->viewVariable('search_data')['published_date']));
        $this->assertTrue(isset($this->viewVariable('search_data')['sb']));
        $this->assertTrue(isset($this->viewVariable('search_data')['sd']));

        $this->assertTrue(isset($this->viewVariable('blog')['id']));
        $this->assertTrue(isset($this->viewVariable('blog')['title']));
        $this->assertTrue(isset($this->viewVariable('blog')['content']));
    }
}
