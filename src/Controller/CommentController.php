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
    public function initialize()
    {
        parent::initialize();
        $this->client = new Client(['base_uri' => env('WAPI_BASE_URI', null)]);
    }

    public function blog($id)
    {
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
        $data = $this->request->getData();
        $this->client->post('comments', ['form_params' => [
            'post' => $data['id'],
            'content' => $data['m'],
            'author' => 1
        ], 'auth' => [env('WAPI_USER'), env('WAPI_PASS')]]);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([]));
    }
}
