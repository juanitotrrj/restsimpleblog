<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;
use Dompdf\Dompdf;
use Cake\View\View;
use Cake\Routing\Router;

/**
 * Blog Controller
 *
 * @property \App\Model\Table\BlogTable $Blog
 */
class BlogController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->client = new Client(['base_uri' => env('WAPI_BASE_URI', null)]);
    }

    /**
     * View method
     *
     * @param string|null $id Blog id.
     * @return \Cake\Network\Response|null
     */
    public function view($id)
    {
        // Base API URI
        $search_data = array_merge(
            ['user' => '', 'search' => '', 'published_date' => '', 'sb' => '', 'sd' => ''], 
            $this->request->getData()
        );

        $blog = $this->getPost($id);

        $this->set(compact('blog', 'search_data'));
        $this->set('_serialize', ['blog', 'search_data']);
        $this->render('/Blog/index');
    }

    public function add()
    {
        $blog = ['title' => 'Blog title', 'content' => 'Blog content', 'image' => ''];
        $search_data = array_merge(
            ['user' => '', 'search' => '', 'published_date' => '', 'sb' => '', 'sd' => ''], 
            $this->request->getData()
        );

        if (!empty($search_data['a_create']))
        {
            $data = $this->request->getData();

            // Save the image
            $media_raw = $this->client->post('media', [  
                'body' => file_get_contents($data['image']['tmp_name']),
                'headers' => [
                    'Content-Type' => $data['image']['type'],
                    'Content-Disposition' => 'attachment; filename=' . $data['image']['name'],
                    'Cache-Control' => 'no-cache'
                ],
                'auth' => [env('WAPI_USER'), env('WAPI_PASS')]
            ])->getBody();
            $media_raw = json_decode((string)$media_raw, true);

            // Save the post
            $post_raw = $this->client->post('posts', ['query' => [
                'title' => strip_tags($data['a_title']),
                'content' => $data['a_content'],
                'featured_media' => $media_raw['id'],
                'status' => 'publish'
            ], 'auth' => [env('WAPI_USER'), env('WAPI_PASS')]])->getBody();
            $post_raw = json_decode((string)$post_raw, true);
            
            $this->Flash->success(__('Post created.'));
            return $this->redirect(['action' => 'view', $post_raw['id']]);
        }

        $this->set(compact('blog', 'id', 'search_data'));
        $this->set('_serialize', ['blog', 'id', 'search_data']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Blog id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        // Base API URI
        $blog = [];

        // Initialize the "Search" filters data
        $search_data = array_merge(
            ['user' => '', 'search' => '', 'published_date' => '', 'sb' => '', 'sd' => ''], 
            $this->request->getData()
        );

        if (!empty($search_data['e_edit']))
        {
            // Update the post
            $this->client->request('POST', 'posts/' . $id, ['query' => [
                'title' => strip_tags($search_data['e_title']),
                'content' => $search_data['e_content']
                ], 'auth' => [env('WAPI_USER'), env('WAPI_PASS')]]);
            $this->Flash->success(__('Update successful.'));
            return $this->redirect(['action' => 'view', $id]);
        }
        else
        {
            // Display the post for editing
            $blog = $this->getPost($id);
        }

        $this->set(compact('blog', 'id', 'search_data'));
        $this->set('_serialize', ['blog', 'id', 'search_data']);
    }

    /**
     * Delete posts
     *
     * @param string|null $id Blog id.
     * @return \Cake\Network\Response|null Redirects to index.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        // Delete blog here
        $this->client->delete('posts/' . $id);
        
        $this->Flash->success(__('The post has been deleted.'));

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Download post in PDF
     * @param  string $type type of download
     * @param  integer $id   id of post
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function download($type = 'pdf', $id)
    {
        $blog = $this->getPost($id);
        
        $view = new View($this->request);
        $view->set(compact('blog'));
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml($view->render('/Blog/Download/' . $type));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setBasePath(Router::url('/', true));
        $dompdf->render();
        $dompdf->stream(strip_tags($blog['title']) . '.pdf');
    }

    /**
     * Get the post
     * @param  integer $id id of the post
     * @return array Blog post content details
     */
    private function getPost($id)
    {
        $blog = [];

        // Perform GET request
        $post = json_decode((string)$this->client->request('GET', 'posts/' . $id)->getBody(), true);

        // Blog post details
        $blog = [
            'id' => $post['id'],
            'title' => $post['title']['rendered'],
            'date' => (new Chronos($post['date']))->toDayDateTimeString(),
            'date_updated' => (new Chronos($post['modified']))->toDayDateTimeString(),
            'content' => $post['content']['rendered'],
        ];

        // Author name and profile link
        $author_raw = json_decode((string)$this->client->request('GET', 'users/' . $post['author'])->getBody(), true);
        $blog['author'] = [
            'href' => $author_raw['link'],
            'name' => $author_raw['name']
        ];

        // Image
        $image_raw = json_decode((string)$this->client->request('GET', 'media', ['query' => ['include' => $post['featured_media']]])->getBody(), true);
        $image = array_pop($image_raw);
        $blog['image'] = $image['guid']['rendered'];

        // Tags
        $blog['tags'] = [];
        if (!empty($post['tags']))
        {
            $tags_raw = json_decode((string)$this->client->request('GET', 'tags', ['query' => ['include' => implode(',', $post['tags'])]])->getBody(), true);
            foreach ($tags_raw as $tag)
            {
                $blog['tags'][] = $tag['name'];
            }
        }

        // Category
        $blog['categories'] = [];
        if (!empty($post['categories']))
        {
            $categories_raw = json_decode((string)$this->client->request('GET', 'categories', ['query' => ['include' => implode(',', $post['categories'])]])->getBody(), true);
            foreach ($categories_raw as $category)
            {
                $blog['categories'][] = $category['name'];
            }
        }

        // Revisions
        $blog['revisions'] = [];
        if (!empty($post['revisions']))
        {
            $revisions_raw = json_decode((string)$this->client->request('GET', 'posts/' . $post['id'] . '/revisions', ['auth' => [env('WAPI_USER'), env('WAPI_PASS')]])->getBody(), true);
            foreach ($revisions_raw as $revision)
            {
                $blog['revisions'][] = [
                    'date' => (new Chronos($revision['date']))->toDateTimeString()
                ];
            }
        }

        return $blog;
    }
}
