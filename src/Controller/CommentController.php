<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;

/**
 * Comment Controller
 *
 * @property \App\Model\Table\CommentTable $Comment
 */
class CommentController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $comment = $this->paginate($this->Comment);

        $this->set(compact('comment'));
        $this->set('_serialize', ['comment']);
    }

    public function blog($id)
    {
        $client = new Client(['base_uri' => 'http://devel2.ordermate.online/wp-json/wp/v2/']);
        $temp = json_decode((string)$client->request('GET', 'comments', ['query' => ['post' => 33]])->getBody(), true);
        $comments = [];
        foreach ($temp as $comment)
        {
            $comments[] = [
                'id' => $comment['id'],
                'name' => $comment['author_name'],
                'date' => (new Chronos($comment['date']))->toDayDateTimeString(),
                'content' => $comment['content']['rendered'],
                'image' => $comment['author_avatar_urls']['48']
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($comments));
    }

    /**
     * View method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $comment = $this->Comment->get($id, [
            'contain' => []
        ]);

        $this->set('comment', $comment);
        $this->set('_serialize', ['comment']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $comment = $this->Comment->newEntity();
        if ($this->request->is('post')) {
            $comment = $this->Comment->patchEntity($comment, $this->request->getData());
            if ($this->Comment->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The comment could not be saved. Please, try again.'));
        }
        $this->set(compact('comment'));
        $this->set('_serialize', ['comment']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $comment = $this->Comment->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $comment = $this->Comment->patchEntity($comment, $this->request->getData());
            if ($this->Comment->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The comment could not be saved. Please, try again.'));
        }
        $this->set(compact('comment'));
        $this->set('_serialize', ['comment']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $comment = $this->Comment->get($id);
        if ($this->Comment->delete($comment)) {
            $this->Flash->success(__('The comment has been deleted.'));
        } else {
            $this->Flash->error(__('The comment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
