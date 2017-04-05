<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;
use Dompdf\Dompdf;
use Cake\View\View;

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
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index($id)
    {
        // Base API URI
        $client = new Client(['base_uri' => 'http://devel2.ordermate.online/wp-json/wp/v2/']);
        $search_data = array_merge(
            ['user' => '', 'search' => '', 'published_date' => '', 'sb' => '', 'sd' => ''], 
            $this->request->getData()
        );

        $blog = [];
        $temp = json_decode((string)$client->request('GET', 'posts', ['query' => ['include' => $id]])->getBody(), true);
        foreach ($temp as $post)
        {
            // Blog post details
            $blog = [
                'id' => $post['id'],
                'title' => $post['title']['rendered'],
                'date' => (new Chronos($post['date']))->toDayDateTimeString(),
                'date_updated' => (new Chronos($post['modified']))->toDayDateTimeString(),
                'content' => $post['content']['rendered'],
            ];

            // Author name and profile link
            $author_raw = json_decode((string)$client->request('GET', 'users/' . $post['author'])->getBody(), true);
            $blog['author'] = [
                'href' => $author_raw['link'],
                'name' => $author_raw['name']
            ];

            // Image
            $image_raw = json_decode((string)$client->request('GET', 'media', ['query' => ['include' => $post['featured_media']]])->getBody(), true);
            $image = array_pop($image_raw);
            $blog['image'] = $image['guid']['rendered'];
        }

        $this->set(compact('blog', 'search_data'));
        $this->set('_serialize', ['blog', 'search_data']);
    }

    /**
     * View method
     *
     * @param string|null $id Blog id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $blog = $this->Blog->get($id, [
            'contain' => []
        ]);

        $this->set('blog', $blog);
        $this->set('_serialize', ['blog']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Blog id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // Base API URI
        $blog = [];
        $client = new Client(['base_uri' => 'http://devel2.ordermate.online/wp-json/wp/v2/']);
        $search_data = array_merge(
            ['user' => '', 'search' => '', 'published_date' => '', 'sb' => '', 'sd' => ''], 
            $this->request->getData()
        );

        if (isset($search_data['e_edit']) && !empty($search_data['e_edit']))
        {
            // Update the post
            $client->request('POST', 'posts/' . $id, ['query' => [
                'title' => $search_data['e_title'],
                'content' => $search_data['e_content']
                ], 'auth' => ['test', '4WB@mgar$#DVq8&%sBt(rj!5']]);
            $this->Flash->success(__('The blog has been successfully updated.'));
            return $this->redirect(['action' => 'index', $id]);
        }
        else
        {
            $temp = json_decode((string)$client->request('GET', 'posts', ['query' => ['include' => $id]])->getBody(), true);
            foreach ($temp as $post)
            {
                $blog = [
                    'title' => $post['title']['rendered'],
                    'content' => $post['content']['rendered']
                ];

                // Image
                $image_raw = json_decode((string)$client->request('GET', 'media', ['query' => ['include' => $post['featured_media']]])->getBody(), true);
                $image = array_pop($image_raw);
                $blog['image'] = $image['guid']['rendered'];
            }
        }

        $this->set(compact('blog', 'id', 'search_data'));
        $this->set('_serialize', ['blog', 'id', 'search_data']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Blog id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        // Delete blog here
        
        $this->Flash->success(__('The blog has been deleted.'));

        return $this->redirect(['action' => 'index']);
    }


    public function download($type = 'pdf', $id)
    {
        $blog = $this->getPost($id);
        
        $view = new View($this->request);
        $view->set(compact('blog'));
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml($view->render('/Blog/Download/' . $type));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $this->response->header('Content-type', 'application/pdf');
        $dompdf->stream();
    }

    private function getPost($id)
    {
        $blog = [];
        $temp = json_decode((string)$this->client->request('GET', 'posts', ['query' => ['include' => $id]])->getBody(), true);
        foreach ($temp as $post)
        {
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
        }

        return $blog;
    }
}
