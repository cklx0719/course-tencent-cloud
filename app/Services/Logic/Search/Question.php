<?php

namespace App\Services\Logic\Search;

use App\Library\Paginator\Adapter\XunSearch as XunSearchPaginator;
use App\Library\Paginator\Query as PagerQuery;
use App\Services\Search\QuestionSearcher as QuestionSearcherService;
use Phalcon\Text;

class Question extends Handler
{

    public function search()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $searcher = new QuestionSearcherService();

        $paginator = new XunSearchPaginator([
            'xs' => $searcher->getXS(),
            'highlight' => $searcher->getHighlightFields(),
            'query' => $params['query'],
            'page' => $page,
            'limit' => $limit,
        ]);

        $pager = $paginator->getPaginate();

        return $this->handleQuestions($pager);
    }

    public function getHotQuery($limit = 10, $type = 'total')
    {
        $searcher = new QuestionSearcherService();

        return $searcher->getHotQuery($limit, $type);
    }

    public function getRelatedQuery($query, $limit = 10)
    {
        $searcher = new QuestionSearcherService();

        return $searcher->getRelatedQuery($query, $limit);
    }

    protected function handleQuestions($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $items = [];

        $baseUrl = kg_cos_url();

        foreach ($pager->items as $item) {

            if (!empty($item['cover']) && !Text::startsWith($item['cover'], 'http')) {
                $item['cover'] = $baseUrl . $item['cover'];
            }

            $lastAnswer = json_decode($item['last_answer'], true);

            if (!empty($lastAnswer['cover']) && !Text::startsWith($lastAnswer['cover'], 'http')) {
                $item['last_answer'] = $baseUrl . $lastAnswer['cover'];
            }

            $acceptAnswer = json_decode($item['accept_answer'], true);

            if (!empty($acceptAnswer['cover']) && !Text::startsWith($acceptAnswer['cover'], 'http')) {
                $item['accept_answer'] = $baseUrl . $acceptAnswer['cover'];
            }

            $items[] = [
                'id' => (int)$item['id'],
                'title' => (string)$item['title'],
                'cover' => (string)$item['cover'],
                'summary' => (string)$item['summary'],
                'bounty' => (int)$item['bounty'],
                'anonymous' => (int)$item['anonymous'],
                'solved' => (int)$item['solved'],
                'create_time' => (int)$item['create_time'],
                'last_reply_time' => (int)$item['last_reply_time'],
                'view_count' => (int)$item['view_count'],
                'like_count' => (int)$item['like_count'],
                'answer_count' => (int)$item['answer_count'],
                'comment_count' => (int)$item['comment_count'],
                'favorite_count' => (int)$item['favorite_count'],
                'category' => json_decode($item['category'], true),
                'tags' => json_decode($item['tags'], true),
                'owner' => json_decode($item['owner'], true),
                'last_replier' => json_decode($item['last_replier'], true),
                'last_answer' => $item['last_answer'],
                'accept_answer' => $item['accept_answer'],
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
