<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Chronos\Chronos;

/**
 * Comment Controller
 *
 * @property \App\Model\Table\CommentTable $Comment
 */
class CommentController extends AppController
{
    public function blog($id)
    {
        $this->request->allowMethod(['get']);

        $temp = json_decode((string)$this->client->request('GET', 'comments', ['query' => ['post' => $id]])->getBody(), true);
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
     * Add comment
     * @return null
     */
    public function add()
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        $this->client->post('comments', ['form_params' => [
            'post' => $data['id'],
            'content' => $data['m'],
            'author' => 2
        ], 'auth' => [env('WAPI_USER'), env('WAPI_PASS')]]);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([]));
    }
}
