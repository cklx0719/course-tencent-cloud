<?php

namespace App\Caches;

use App\Models\Question as QuestionModel;

class MaxQuestionId extends Cache
{

    protected $lifetime = 365 * 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return 'max_question_id';
    }

    public function getContent($id = null)
    {
        $question = QuestionModel::findFirst(['order' => 'id DESC']);

        return $question->id ?? 0;
    }

}
