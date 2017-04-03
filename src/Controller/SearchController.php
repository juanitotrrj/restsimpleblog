<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\SearchForm;
use App\Form\UsersForm;
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;

/**
 * Search Controller
 *
 * @property \App\Model\Table\SearchTable $Search
 */
class SearchController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // Base API URI
        $client = new Client(['base_uri' => 'http://devel2.ordermate.online/wp-json/wp/v2/']);

        // Get POST data, if any
        $post_data = array_merge(['user' => '', 'search' => '', 'published_date' => ''], $this->request->getData());

        // init
        $results = [];
        $api_args = [];

        // Get all users
        $user_response = $client->request('GET', 'users');
        $users = json_decode((string)$user_response->getBody(), true);

        if ($this->request->is(['post']))
        {
            // Validate submitted data in Form
            $validation = (new SearchForm())->validate($this->request->getData());
            if (!$validation)
            {
                $this->Flash->error(__('There were invalid data submitted. Please, check your fields.'));            
            }
            else
            {
                // Get sort order & direction
                $sort = [$this->request->getQuery('sort') => $this->request->getQuery('sdir')];

                if (!empty($post_data))
                {
                    // If User search filter exists
                    $user_args = [];
                    if (!empty($post_data['user']))
                    {
                        // User comments
                        $user_comments = json_decode((string)$client->request('GET', 'comments', [
                            'query' => ['author' => $post_data['user']],
                            'auth' => ['test', '4WB@mgar$#DVq8&%sBt(rj!5']
                        ])
                            ->getBody(), true);

                        // Post IDs
                        $post_ids = [];
                        foreach ($user_comments as $comment)
                        {
                            $post_ids[] = $comment['post'];
                        }

                        $user_args = [
                            'include' => implode(',', $post_ids)
                        ];
                    }
                    
                    // Keyword
                    $search_args = [];
                    if (!empty($post_data['search']))
                    {
                        $search_args = [
                            'search' => $post_data['search']
                        ];
                    }

                    // Published date
                    $date_args = [];
                    if (!empty($post_data['published_date']))
                    {
                        $time = new Chronos($post_data['published_date']);
                        $after_iso_date = $time
                            ->addDays(-1)
                            ->toIso8601String();
                        $before_iso_date = $time
                            ->addDays(1)
                            ->toIso8601String();
                        $date_args = [
                            'after' => $after_iso_date,
                            'before' => $before_iso_date,
                        ];
                    }

                    $api_args = array_merge($api_args, $date_args, $search_args, $user_args);
                }
            }
        }

        $temp = json_decode((string)$client->request('GET', 'posts', ['query' => $api_args])->getBody(), true);
        foreach ($temp as $post)
        {
            $author_raw = json_decode((string)$client->request('GET', 'users/' . $post['author'])->getBody(), true);
            $results[] = [
                'title' => $post['title']['rendered'],
                'published_date' => (new Chronos($post['date_gmt']))->toFormattedDateString(),
                'author' => ['name' => $author_raw['name'], 'profile' => $author_raw['link']],
            ];
        }

        $this->set(compact('results', 'users', 'post_data'));
        $this->set('_serialize', ['results', 'users']);
    }

    /**
     * View method
     *
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $search = $this->Search->get($id, [
            'contain' => []
        ]);

        $this->set('search', $search);
        $this->set('_serialize', ['search']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $search = $this->Search->newEntity();
        if ($this->request->is('post')) {
            $search = $this->Search->patchEntity($search, $this->request->getData());
            if ($this->Search->save($search)) {
                $this->Flash->success(__('The search has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The search could not be saved. Please, try again.'));
        }
        $this->set(compact('search'));
        $this->set('_serialize', ['search']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $search = $this->Search->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $search = $this->Search->patchEntity($search, $this->request->getData());
            if ($this->Search->save($search)) {
                $this->Flash->success(__('The search has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The search could not be saved. Please, try again.'));
        }
        $this->set(compact('search'));
        $this->set('_serialize', ['search']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Search id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $search = $this->Search->get($id);
        if ($this->Search->delete($search)) {
            $this->Flash->success(__('The search has been deleted.'));
        } else {
            $this->Flash->error(__('The search could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
