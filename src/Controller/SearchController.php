<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\SearchForm;
use Cake\Chronos\Chronos;

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
        // Get POST data, if any, and sort order & direction
        $post_data = array_merge(
            ['user' => '', 'search' => '', 'published_date' => '', 'sb' => '', 'sd' => ''], 
            $this->request->getData()
        );

        // init
        $results = [];
        $api_args = [];

        // Get all users
        $user_response = $this->client->request('GET', 'users');
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
                if (!empty($post_data))
                {
                    // If User search filter exists
                    $user_args = [];
                    if (!empty($post_data['user']))
                    {
                        // User comments
                        $user_comments = json_decode((string)$this->client->request('GET', 'comments', [
                            'query' => ['author' => $post_data['user']],
                            'auth' => [env('WAPI_USER'), env('WAPI_PASS')]
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

                    // Sorting
                    $sort_args = [];
                    if (!empty($post_data['sb']))
                    {
                        $sort_args = [
                            'orderby' => $post_data['sb'], 
                            'order' => strtolower($post_data['sd'])
                        ];
                    }

                    // WP-API args
                    $api_args = array_merge($api_args, $date_args, $search_args, $user_args, $sort_args);
                }
            }
        }

        $temp = json_decode((string)$this->client->request('GET', 'posts', ['query' => $api_args])->getBody(), true);
        foreach ($temp as $post)
        {
            $author_raw = json_decode((string)$this->client->request('GET', 'users/' . $post['author'])->getBody(), true);
            $results[] = [
                'id' => $post['id'],
                'title' => $post['title']['rendered'],
                'published_date' => (new Chronos($post['date_gmt']))->toFormattedDateString(),
                'author' => ['name' => $author_raw['name'], 'profile' => $author_raw['link']],
            ];
        }

        $this->set(compact('results', 'users', 'post_data'));
        $this->set('_serialize', ['results', 'users']);
    }
}
