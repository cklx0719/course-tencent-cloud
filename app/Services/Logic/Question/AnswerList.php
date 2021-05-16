<?php

namespace App\Services\Logic\Question;

use App\Builders\AnswerList as AnswerListBuilder;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Question as QuestionModel;
use App\Repos\Answer as AnswerRepo;
use App\Repos\AnswerLike as AnswerLikeRepo;
use App\Services\Logic\QuestionTrait;
use App\Services\Logic\Service as LogicService;

class AnswerList extends LogicService
{

    use QuestionTrait;

    public function handle($id)
    {
        $question = $this->checkQuestion($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['question_id'] = $question->id;
        $params['published'] = QuestionModel::PUBLISH_APPROVED;
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

        $users = $builder->getUsers($answers);

        $meMappings = $this->getMeMappings($answers);

        $items = [];

        foreach ($answers as $answer) {

            $answer['content'] = kg_parse_markdown($answer['content']);

            $owner = $users[$answer['owner_id']] ?? new \stdClass();
            $me = $meMappings[$answer['id']];

            $items[] = [
                'id' => $answer['id'],
                'content' => $answer['content'],
                'anonymous' => $answer['anonymous'],
                'accepted' => $answer['accepted'],
                'published' => $answer['published'],
                'like_count' => $answer['like_count'],
                'comment_count' => $answer['comment_count'],
                'create_time' => $answer['create_time'],
                'update_time' => $answer['update_time'],
                'owner' => $owner,
                'me' => $me,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

    protected function getMeMappings($answers)
    {
        $user = $this->getCurrentUser(true);

        $likeRepo = new AnswerLikeRepo();

        $likedIds = [];

        if ($user->id > 0) {
            $likes = $likeRepo->findByUserId($user->id)
                ->filter(function ($like) {
                    if ($like->deleted == 0) {
                        return $like;
                    }
                });
            $likedIds = array_column($likes, 'answer_id');
        }

        $result = [];

        foreach ($answers as $answer) {
            $result[$answer['id']] = [
                'liked' => in_array($answer['id'], $likedIds) ? 1 : 0,
            ];
        }

        return $result;
    }

}