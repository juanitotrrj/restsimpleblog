<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;

/**
 * Blog Controller
 *
 * @property \App\Model\Table\BlogTable $Blog
 */
class BlogController extends AppController
{

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
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $blog = $this->Blog->newEntity();
        if ($this->request->is('post')) {
            $blog = $this->Blog->patchEntity($blog, $this->request->getData());
            if ($this->Blog->save($blog)) {
                $this->Flash->success(__('The blog has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The blog could not be saved. Please, try again.'));
        }
        $this->set(compact('blog'));
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
        $blog = $this->Blog->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $blog = $this->Blog->patchEntity($blog, $this->request->getData());
            if ($this->Blog->save($blog)) {
                $this->Flash->success(__('The blog has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The blog could not be saved. Please, try again.'));
        }
        $this->set(compact('blog'));
        $this->set('_serialize', ['blog']);
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
        $blog = $this->Blog->get($id);
        if ($this->Blog->delete($blog)) {
            $this->Flash->success(__('The blog has been deleted.'));
        } else {
            $this->Flash->error(__('The blog could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
