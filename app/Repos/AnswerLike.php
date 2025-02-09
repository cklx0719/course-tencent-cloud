<?php

namespace App\Repos;

use App\Models\AnswerLike as AnswerLikeModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class AnswerLike extends Repository
{

    /**
     * @param int $answerId
     * @param int $userId
     * @return AnswerLikeModel|Model|bool
     */
    public function findAnswerLike($answerId, $userId)
    {
        return AnswerLikeModel::findFirst([
            'conditions' => 'answer_id = :answer_id: AND user_id = :user_id:',
            'bind' => ['answer_id' => $answerId, 'user_id' => $userId],
        ]);
    }

    /**
     * @param int $userId
     * @return ResultsetInterface|Resultset|AnswerLikeModel[]
     */
    public function findByUserId($userId)
    {
        return AnswerLikeModel::query()
            ->where('user_id = :user_id:', ['user_id' => $userId])
            ->execute();
    }

}
