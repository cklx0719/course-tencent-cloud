<?php

namespace App\Services\Search;

use App\Models\Question as QuestionModel;
use App\Repos\Answer as AnswerRepo;
use App\Repos\Category as CategoryRepo;
use App\Repos\User as UserRepo;
use Phalcon\Mvc\User\Component;

class QuestionDocument extends Component
{

    /**
     * 设置文档
     *
     * @param QuestionModel $question
     * @return \XSDocument
     */
    public function setDocument(QuestionModel $question)
    {
        $doc = new \XSDocument();

        $data = $this->formatDocument($question);

        $doc->setFields($data);

        return $doc;
    }

    /**
     * 格式化文档
     *
     * @param QuestionModel $question
     * @return array
     */
    public function formatDocument(QuestionModel $question)
    {
        if (empty($question->summary)) {
            $question->summary = kg_parse_summary($question->content);
        }

        if (is_array($question->tags) || is_object($question->tags)) {
            $question->tags = kg_json_encode($question->tags);
        }

        $category = '{}';

        if ($question->category_id > 0) {
            $category = $this->handleCategory($question->category_id);
        }

        $owner = '{}';

        if ($question->owner_id > 0) {
            $owner = $this->handleUser($question->owner_id);
        }

        $lastReplier = '{}';

        if ($question->last_replier_id > 0) {
            $lastReplier = $this->handleUser($question->last_replier_id);
        }

        $lastAnswer = '{}';

        if ($question->last_answer_id > 0) {
            $lastAnswer = $this->handleAnswer($question->last_answer_id);
        }

        $acceptAnswer = '{}';

        if ($question->accept_answer_id > 0) {
            $acceptAnswer = $this->handleAnswer($question->accept_answer_id);
        }

        return [
            'id' => $question->id,
            'title' => $question->title,
            'cover' => $question->cover,
            'summary' => $question->summary,
            'tags' => $question->tags,
            'category_id' => $question->category_id,
            'owner_id' => $question->owner_id,
            'create_time' => $question->create_time,
            'last_reply_time' => $question->last_reply_time,
            'bounty' => $question->bounty,
            'anonymous' => $question->anonymous,
            'solved' => $question->solved,
            'view_count' => $question->view_count,
            'like_count' => $question->like_count,
            'answer_count' => $question->answer_count,
            'comment_count' => $question->comment_count,
            'favorite_count' => $question->favorite_count,
            'category' => $category,
            'owner' => $owner,
            'last_replier' => $lastReplier,
            'last_answer' => $lastAnswer,
            'accept_answer' => $acceptAnswer,
        ];
    }

    protected function handleUser($id)
    {
        $userRepo = new UserRepo();

        $user = $userRepo->findById($id);

        return kg_json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }

    protected function handleCategory($id)
    {
        $categoryRepo = new CategoryRepo();

        $category = $categoryRepo->findById($id);

        return kg_json_encode([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    protected function handleAnswer($id)
    {
        $answerRepo = new AnswerRepo();

        $answer = $answerRepo->findById($id);

        return kg_json_encode([
            'id' => $answer->id,
            'summary' => $answer->summary,
            'cover' => $answer->cover,
        ]);
    }

}
