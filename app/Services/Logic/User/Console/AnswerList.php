<?php

namespace App\Services\Logic\User\Console;

use App\Builders\AnswerList as AnswerListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Repos\Answer as AnswerRepo;
use App\Services\Logic\Service as LogicService;

class AnswerList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['owner_id'] = $user->id;
        $params['deleted'] = 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $answerRepo = new AnswerRepo();

        $pager = $answerRepo->paginate($params, $sort, $page, $limit);

        return $this->handleAnswers($pager);
    }

    protected function handleAnswers($pager)
    {
        if ($pager->total_items == 0) {
            return $pager;
        }

        $builder = new AnswerListBuilder();

        $answers = $pager->items->toArray();

        $questions = $builder->getQuestions($answers);

        $users = $builder->getUsers($answers);

        $items = [];

        foreach ($answers as $answer) {

            $answer['summary'] = kg_parse_summary($answer['content'], 64);

            $question = $questions[$answer['question_id']] ?? new \stdClass();
            $owner = $users[$answer['owner_id']] ?? new \stdClass();

            $items[] = [
                'id' => $answer['id'],
                'summary' => $answer['summary'],
                'published' => $answer['published'],
                'accepted' => $answer['accepted'],
                'comment_count' => $answer['comment_count'],
                'like_count' => $answer['like_count'],
                'create_time' => $answer['create_time'],
                'update_time' => $answer['update_time'],
                'question' => $question,
                'owner' => $owner,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
